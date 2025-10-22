<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Postulante;
use GuzzleHttp\Client;
use App\Models\ProjectHasPostulantes;
use App\Models\Document;
// use App\Models\PostulantesDocuments;
use App\Models\Assignment;
use App\Models\Land_project;
use App\Models\Discapacidad;
use App\Models\Parentesco;
use App\Models\Persona;
use App\Models\SIG005;
use App\Models\SIG006;
use App\Models\SHMCER;
use App\Models\PRMCLI;
use App\Models\IVMSOL;
use App\Models\ProjectStatus;
use App\Models\PostulanteHasDiscapacidad;
use App\Models\PostulanteHasBeneficiary;
use App\Http\Requests\StorePostulante;
use PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class PostulantesController extends Controller
{
    //

    public function __construct()
    {
        $this->middleware('auth');
        $this->photos_path = public_path('/images');

    }

    public function index($id)
    {
        $title = "Lista de Postulantes";

        // Obtener proyecto con relaciones necesarias para la vista
        $project = Project::with([
            'getState',
            'getCity',
            'getModality',
            'getSat',
            'getEstado.getStage' // Si necesitás el stage del estado
        ])->findOrFail($id);

        // Traer todos los postulantes con todos los miembros y datos de postulante
        $postulantes = ProjectHasPostulantes::with([
            'getPostulante', // Datos del postulante
            'getMembers.getPostulante' // Todos los miembros y sus datos
        ])->where('project_id', $id)->get();

        // Precalcular ingresos y niveles para evitar llamadas repetidas en blade
        $postulanteIds = $postulantes->pluck('postulante_id')->filter()->toArray();

        $ingresos = ProjectHasPostulantes::getIngresosBatch($postulanteIds);
        $niveles = ProjectHasPostulantes::getNivelesBatch($postulanteIds);

        return view('postulantes.index', compact('project', 'title', 'postulantes', 'ingresos', 'niveles'));
    }


    public function create(Request $request, $id)
    {
        if (!$request->filled('cedula')) {
            return redirect()->back()->with('error', 'Ingrese Cédula');
        }

        $cedula = $request->input('cedula');

        // Verificación previa de registros
        $mensajes = $this->verificarRestriccionesGenerales($cedula);
        if ($mensajes) {
            return redirect()->back()->with('status', $mensajes);
        }

        // Intentar obtener datos desde la API o base local
        $datos = $this->obtenerDatosPersona($cedula);
        if (!$datos) {
            return redirect()->back()->with('status', 'No se pudieron recuperar los datos desde el servicio ni desde la base local.');
        }

        // Validar que sea mayor de 18 años
        $validacionEdad = $this->validarEdadMinima($datos['fecha']);
        if (!$validacionEdad['esValido']) {
            return redirect()->back()->with('error', $validacionEdad['mensaje']);
        }

        // Extraemos variables para pasar a la vista
        $nombre = $datos['nombre'] ?? '';
        $apellido = $datos['apellido'] ?? '';
        $fecha = $datos['fecha'] ?? '';
        $sexo = $datos['sexo'] ?? '';
        $nac = $datos['nac'] ?? '';
        $est = $datos['est'] ?? '';

        $nroexp = $datos['cedula'];
        $title = "Agregar Postulante";
        $project_id = Project::find($id);
        $discapacdad = Discapacidad::all();

        return view('postulantes.create', compact(
            'nroexp', 'cedula', 'nombre', 'apellido', 'fecha', 'sexo',
            'nac', 'est', 'title', 'project_id', 'discapacdad'
        ));
    }

    /**
     * Valida que la persona tenga al menos 18 años
     */
    private function validarEdadMinima($fechaNacimiento)
    {
        try {
            $fechaNac = new \DateTime($fechaNacimiento);
            $hoy = new \DateTime();
            $edad = $hoy->diff($fechaNac)->y;

            if ($edad < 18) {
                return [
                    'esValido' => false,
                    'mensaje' => 'Un menor de edad no puede ser postulante o conyuge. La persona debe tener al menos 18 años.'
                ];
            }

            return [
                'esValido' => true,
                'mensaje' => ''
            ];
        } catch (\Exception $e) {
            \Log::error('Error al validar edad', [
                'fecha' => $fechaNacimiento,
                'error' => $e->getMessage()
            ]);

            return [
                'esValido' => false,
                'mensaje' => 'Error al validar la fecha de nacimiento. Verifique que la fecha sea válida.'
            ];
        }
    }

    private function obtenerDatosPersona($cedula)
    {
        try {
            $client = new \GuzzleHttp\Client();

            $auth = $client->post('https://sii.paraguay.gov.py/security', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
                'json' => [
                    'username' => 'muvhConsulta',
                    'password' => '*Sipp*2025**'
                ]
            ]);

            $tokenData = json_decode($auth->getBody()->getContents());

            if (empty($tokenData->success)) {
                throw new \Exception("API sin éxito");
            }

            $response = $client->get("https://sii.paraguay.gov.py/frontend-identificaciones/api/persona/obtenerPersonaPorCedula/{$cedula}", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $tokenData->token,
                    'Accept' => 'application/json'
                ]
            ]);

            $persona = json_decode($response->getBody()->getContents());

            if (!isset($persona->obtenerPersonaPorNroCedulaResponse->return)) {
                \Log::warning('API respondió pero sin datos válidos', ['cedula' => $cedula, 'respuesta' => $persona]);
                return null;
            }

            $p = $persona->obtenerPersonaPorNroCedulaResponse->return;

            if (!$p || isset($p->error)) {
                return null;
            }

            return [
                'nombre' => $p->nombres,
                'apellido' => $p->apellido,
                'cedula' => $p->cedula,
                'sexo' => $p->sexo,
                'fecha' => Carbon::parse($p->fechNacim)->toDateString(),
                'nac' => $p->nacionalidadBean ?? '',
                'est' => $p->estadoCivil ?? ''
            ];
        } catch (\Exception $e) {
            // Solo error de la API, no incluye fallo en BD
            \Log::warning('Error al obtener datos desde la API, intento con BD local', [
                'cedula' => $cedula,
                'mensaje' => $e->getMessage()
            ]);

            $persona = \App\Models\Persona::where('BDICed', $cedula)->first();

            if (!$persona) {
                \Log::error('No se encontró la persona en la BD local', ['cedula' => $cedula]);
                return null;
            }

            return [
                'nombre' => $persona->BDINom,
                'apellido' => $persona->BDIAPE,
                'cedula' => $persona->BDICed,
                'sexo' => $persona->BDISexo,
                'fecha' => $persona->BDIFecNac,
                'nac' => '',
                'est' => $persona->BDIEstCiv
            ];
        }
    }



    public function createmiembro(Request $request, $id, $x)
    {
        $proyectoEstado = ProjectStatus::where('project_id', $id)->latest()->first();
        $ultimoEstado = $proyectoEstado->stage_id ?? null;

        if (!$request->filled('cedula')) {
            return redirect()->back()->with('error', 'Ingrese Cédula');
        }

        $cedula = $request->input('cedula');

        // Verifica restricciones generales
        if ($msg = $this->verificarRestriccionesGenerales($cedula)) {
            return redirect()->back()->with('status', $msg);
        }

        // Consulta datos desde el SII
        $datosPersona = $this->obtenerDatosPersona($cedula);
        if (isset($datosPersona['error'])) {
            return redirect()->back()->with('status', $datosPersona['error']);
        }

        // Extraer variables individuales para evitar modificar la vista
        $nombre = $datosPersona['nombre'] ?? '';
        $apellido = $datosPersona['apellido'] ?? '';
        $fecha = $datosPersona['fecha'] ?? '';
        $sexo = $datosPersona['sexo'] ?? '';
        $nac = $datosPersona['nac'] ?? '';
        $est = $datosPersona['est'] ?? '';

        $title = "Agregar Miembro Familiar";
        $project_id = Project::find($id);
        $nroexp = $cedula;

        $par = [1, 8];
        if ($ultimoEstado === null) {
            $parentesco = Parentesco::whereIn('id', $par)->orderBy('name', 'asc')->get();
        } else {
            $parentesco = Parentesco::all();
        }

        $discapacdad = Discapacidad::all();
        $idpostulante = $x;

        return view('postulantes.ficha.createmiembro', compact(
            'nroexp', 'cedula', 'nombre', 'apellido', 'fecha', 'sexo',
            'nac', 'est', 'title', 'project_id',
            'discapacdad', 'parentesco', 'idpostulante'
        ));
    }


    private function verificarRestriccionesGenerales($cedula)
    {
        // Verificar si existe un expediente
        $expediente = SIG005::where('NroExpPer', $cedula)
            ->where('TexCod', 118)
            ->orderBy('NroExp', 'desc')
            ->first();
        if ($expediente) {
            // Verificar que el archivo esté en estado C o H
            $archivo = SIG006::where('NroExp', $expediente->NroExp)
                ->whereIn('DEExpEst', ['C', 'H'])
                ->exists();


            if (!$archivo) {
                return 'Ya existe expendiente de FICHA DE PRE-INSCRIPCION FONAVIS-SVS!!!.';
            }
        }

        // Evaluar todas las restricciones, se haya pasado o no la condición de expediente
        if (Postulante::where('cedula', $cedula)->exists()) {
            return 'Ya existe el postulante!';
        }
        // $shmcer=SHMCER::where('CerPosCod', $cedula)->whereNotIn('CerEst', [2, 7, 8, 12])->exists();
        // dd($shmcer);
        if (SHMCER::where('CerPosCod', $cedula)->whereNotIn('CerEst', [2, 7, 8, 12])->exists()) {
            return 'Ya cuenta con certificado de Subsidio como Titular!';
        }
        // $shmcerCge=SHMCER::where('CerCoCI', $cedula)->whereNotIn('CerEst', [2, 7, 8, 12])->exists();
        // dd($shmcerCge);
        if (SHMCER::where('CerCoCI', $cedula)->whereNotIn('CerEst', [2, 7, 8, 12])->exists()) {
            return 'Ya cuenta con certificado de Subsidio como Conyuge!';
        }
        // $prmcli=PRMCLI::where('PerCod', $cedula)->where('PylCod', '!=', 'P.F.')->exists();
        // dd($prmcli);
        if (PRMCLI::where('PerCod', $cedula)->where('PylCod', '!=', 'P.F.')->exists()) {
            return 'Ya cuenta con Beneficios en la Institución!';
        }

        // $ivmsol=IVMSOL::where('SolPerCod', $cedula)->where('SolEtapa', 'B')->exists();
        // dd($ivmsol);
        if (IVMSOL::where('SolPerCod', $cedula)->where('SolEtapa', 'B')->exists()) {
            return 'Ya es Beneficiario Final!';
        }

        // $solicitante = IVMSOL::where('SolPerCge', $cedula)->first();
        // if ($solicitante) {
        //     $carterasol = PRMCLI::where('PerCod', trim($solicitante->SolPerCod))
        //         ->where('PylCod', '!=', 'P.F.')
        //         ->exists();

        //     if ($carterasol) {
        //         return 'Ya cuenta con Beneficios en la Institución como Conyuge!';
        //     }
        // }

        // $ivmsolcge = IVMSOL::where('SolPerCge', $cedula)->first();
        // // dd($ivmsolcge);
        // if ($ivmsolcge) {
        //         return 'Ya cuenta con Beneficios en la Institución como Conyuge!';
        // }

        if (IVMSOL::where('SolPerCge', $cedula)->where('SolEtapa', 'B')->exists()) {
            return 'Ya cuenta con Beneficios en la Institución como Conyuge!';
        }


        return null; // Alta permitida
    }


    public function store(StorePostulante $request)
    {
        $input = $request->except(['_token', 'project_id', 'discapacidad_id']);

        DB::transaction(function () use ($request, $input) {
            // Crear el postulante
            $postulante = Postulante::create($input);

            // Asociarlo al proyecto
            ProjectHasPostulantes::create([
                'project_id'    => $request->project_id,
                'postulante_id' => $postulante->id,
            ]);

            // Asociar discapacidad
            PostulanteHasDiscapacidad::create([
                'discapacidad_id' => $request->discapacidad_id,
                'postulante_id'   => $postulante->id,
            ]);
        });

        return redirect('projects/'.$request->project_id.'/postulantes')
            ->with('success', 'Se ha agregado un nuevo Postulante!');
    }


   public function storeEditPostulante(StorePostulante $request)
    {
        DB::transaction(function () use ($request) {
            // Encuentra el postulante por ID
            $postulante = Postulante::findOrFail($request->postulante_id);

            // Rellena los atributos validados
            $postulante->fill($request->except(['_token', 'project_id', 'discapacidad_id']));
            $postulante->save(); // SoftDeletes y Auditing funcionan automáticamente

            // Si quieres actualizar discapacidad, ejemplo:
            if ($request->filled('discapacidad_id')) {
                PostulanteHasDiscapacidad::updateOrCreate(
                    ['postulante_id' => $postulante->id],
                    ['discapacidad_id' => $request->discapacidad_id]
                );
            }
        });

        return redirect('projects/'.$request->project_id.'/postulantes')
            ->with('success', 'Se ha editado correctamente el Postulante!');
    }

    public function storemiembro(Request $request)
    {
        // Validación de campos
        $request->validate([
            'cedula' => 'required|string|max:255',
            'parentesco_id' => 'required|integer|exists:parentesco,id',
            'birthdate' => 'required|date',
            'discapacidad_id' => 'nullable|integer|exists:discapacidad,id',
        ]);

        $parentescoId = $request->input('parentesco_id');
        $fechaNacimiento = $request->input('birthdate');

        // Validación de edad para cónyuge o titular
        if (in_array($parentescoId, [1, 8])) {
            $validacionEdad = $this->validarEdadMinima($fechaNacimiento);
            if (!$validacionEdad['esValido']) {
                return redirect('projects/' . $request->project_id . '/postulantes')
                    ->with('error', $validacionEdad['mensaje']);
            }
        }

        DB::transaction(function () use ($request) {
            // Crear el nuevo miembro
            $postulante = Postulante::create($request->except(['_token', 'project_id', 'discapacidad_id', 'postulante_id']));

            // Relación con el postulante principal
            PostulanteHasBeneficiary::create([
                'postulante_id' => $request->postulante_id,
                'miembro_id'    => $postulante->id,
                'parentesco_id' => $request->parentesco_id,
            ]);

            // Relación de discapacidad si aplica
            if ($request->filled('discapacidad_id')) {
                PostulanteHasDiscapacidad::create([
                    'postulante_id'   => $postulante->id,
                    'discapacidad_id' => $request->discapacidad_id,
                ]);
            }
        });

        return redirect('projects/' . $request->project_id . '/postulantes')
            ->with('success', 'Se ha agregado un nuevo Miembro!');
    }

    public function storemiembroeditar(Request $request)
    {
        DB::transaction(function () use ($request) {
            // Editar los datos del miembro
            $postulante = Postulante::findOrFail($request->postulante_id);
            $postulante->fill($request->except(['_token', 'project_id', 'discapacidad_id']));
            $postulante->save();

            // Actualizar discapacidad si aplica
            if ($request->filled('discapacidad_id')) {
                PostulanteHasDiscapacidad::updateOrCreate(
                    ['postulante_id' => $postulante->id],
                    ['discapacidad_id' => $request->discapacidad_id]
                );
            }
        });

        return redirect('projects/'.$request->project_id.'/postulantes/'.$request->postulante_id)
            ->with('success', 'Se ha editado correctamente el Miembro!');
    }

    public function show($id,$idpostulante)
    {
        $postulante=Postulante::find($idpostulante);
        $project = Project::find($id);
        $title="Resumen Postulante ";
        //dd($project);
        $tipoproy = Land_project::where('land_id',$project->land_id)->first();
        // $documentos = PostulantesDocuments::where('postulante_id',$idpostulante)->get();
        // $docproyecto = Assignment::where('project_type_id',$tipoproy->project_type_id)
        // ->whereNotIn('document_id', $documentos->pluck('document_id'))
        // ->where('category_id',2)
        // ->get();
        $miembros = PostulanteHasBeneficiary::where('postulante_id',$postulante->id)->get();
        //$docproyecto = $docproyecto->whereNotIn('document_id', $documentos->pluck('document_id'));
        return view('postulantes.show',compact('title','project','miembros','postulante'));
    }

    public function edit($id,$idpostulante)
    {
        $title="Editar Postulante";
        $project=Project::find($id);
        $postulante=Postulante::find($idpostulante);
        $nombre = $postulante->first_name;
        $apellido = $postulante->last_name;
        $cedula = $postulante->cedula;
        $sexo = $postulante->gender;
        $project_id = Project::find($id);
        $nac = $postulante->nacionalidad;
        $est = $postulante->marital_status;
        $fecha = $postulante->birthdate;
        $discapacdad = Discapacidad::all();
        $disc = PostulanteHasDiscapacidad::where('postulante_id',$postulante->id)->first();

        return view('postulantes.create',compact('title','project','postulante','apellido','cedula','sexo','project_id',
                                                'nombre','nac','est','fecha','discapacdad','disc'));
    }

    public function editarPostulante($id,$idpostulante)
    {
        $title="Editar Postulante";
        $project=Project::find($id);
        $postulante=Postulante::find($idpostulante);
        $nombre = $postulante->first_name;
        $apellido = $postulante->last_name;
        $cedula = $postulante->cedula;
        $sexo = $postulante->gender;
        $project_id = Project::find($id);
        $nac = $postulante->nacionalidad;
        $est = $postulante->marital_status;
        $fecha = $postulante->birthdate;
        $discapacdad = Discapacidad::all();
        $disc = PostulanteHasDiscapacidad::where('postulante_id',$postulante->id)->first();

        return view('postulantes.edit',compact('title','project','postulante','apellido','cedula','sexo','project_id',
                                                'nombre','nac','est','fecha','discapacdad','disc'));
    }



    public function editmiembro($id, $idpostulante)
    {
        $title = "Editar Miembro";

        // Obtener el proyecto y postulante (evitando consultas duplicadas)
        $project = Project::select('id', 'name')->findOrFail($id);
        $postulante = Postulante::findOrFail($idpostulante);

        // Variables para la vista
        $nombre    = $postulante->first_name;
        $apellido  = $postulante->last_name;
        $cedula    = $postulante->cedula;
        $sexo      = $postulante->gender;
        $project_id = $project; // se mantiene para compatibilidad con la vista
        $nac       = $postulante->nacionalidad;
        $est       = $postulante->marital_status;
        $fecha     = $postulante->birthdate;

        // Listado de discapacidades
        $discapacdad = Discapacidad::all();

        // Discapacidad actual del postulante (si tiene)
        $disc = PostulanteHasDiscapacidad::where('postulante_id', $postulante->id)
            ->select('id', 'discapacidad_id')
            ->first();

        // Verificar estado del proyecto y cargar los parentescos según corresponda
        $estado = ProjectStatus::where('project_id', $id)->first();

        if (empty($estado)) {
            $par = [1, 8];
            $parentesco = Parentesco::whereIn('id', $par)->get();
        } else {
            $parentesco = Parentesco::all();
        }

        // Obtener la relación entre el postulante principal y el miembro
        $parent = PostulanteHasBeneficiary::where('miembro_id', $postulante->id)
            ->select('postulante_id', 'miembro_id')
            ->firstOrFail();

        $idpostulante = $parent->postulante_id;
        $idmiembro    = $parent->miembro_id;

        // Retornar vista con los mismos nombres de variables
        return view('postulantes.ficha.editmiembro', compact(
            'title',
            'project',
            'postulante',
            'apellido',
            'cedula',
            'sexo',
            'project_id',
            'nombre',
            'nac',
            'est',
            'fecha',
            'discapacdad',
            'disc',
            'parentesco',
            'idpostulante',
            'idmiembro'
        ));
    }

    public function update(Request $request)
    {
        //
        //return $request;
        $postulante = Postulante::find($request->input("id"));
        $postulante->localidad = $request->input("localidad");
        $postulante->address = $request->input("address");
        $postulante->cedula = $request->input("cedula");
        $postulante->phone = $request->input("phone");
        $postulante->asentamiento = $request->input("asentamiento");
        $postulante->ingreso = $request->input("ingreso");
        $postulante->mobile = $request->input("mobile");
        $postulante->save();

        $disc = PostulanteHasDiscapacidad::find($request->input("disc_id"));
        $disc->discapacidad_id=$request->discapacidad_id;
        $disc->save();






        return redirect('projects/'.$request->input("project_id").'/postulantes')->with('success', 'El postulante fue actualizado!');
    }

    public function updatemiembro(Request $request)
    {
        //return $request;
        //return $request->parent_id;
        $postulante = Postulante::find($request->parent_id);
        $postulante->localidad = $request->input("localidad");
        $postulante->address = $request->input("address");
        $postulante->cedula = $request->input("cedula");
        $postulante->phone = $request->input("phone");
        $postulante->asentamiento = $request->input("asentamiento");
        $postulante->ingreso = $request->input("ingreso");
        $postulante->mobile = $request->input("mobile");
        $postulante->save();

        $disc = PostulanteHasDiscapacidad::find($request->disc_id);
        $disc->discapacidad_id=$request->discapacidad_id;
        $disc->save();


        $parent = PostulanteHasBeneficiary::where('miembro_id', $request->parent_id)->first();
        $parent->parentesco_id=$request->parentesco_id;
        $parent->save();

        return redirect('projects/'.$request->input("project_id").'/postulantes/'.$request->input("postulante_id"))->with('success', 'El miembro fue actualizado!');
    }

    // public function generatePDF($id)
    // {
    //     $project=Project::find($id);
    //     $postulantes = ProjectHasPostulantes::where('project_id',$id)->get();
    //     $contar = count($postulantes);
    //     $pdf = PDF::loadView('postulantesPDF', compact('project','postulantes', 'contar'))->setPaper('a4', 'landscape');

    //     return $pdf->download('Listadopostulantes.pdf');
    // }

    public function generatePDF($id)
    {
        // Eager loading de todas las relaciones necesarias
        $project = Project::with([
            'getState',
            'getModality',
            'getCity',
            'getLand',
            'getTypology',
            'getEstado.getStage',
            'getSat',
        ])->findOrFail($id);

        // Query optimizada con eager loading
        $postulantes = ProjectHasPostulantes::where('project_id', $id)
            ->with([
                'getPostulante:id,first_name,last_name,cedula,birthdate,ingreso',
            ])
            ->get();

        // Preparar datos precalculados para el PDF
        $postulantesData = $postulantes->map(function($post) {
            $postulante = $post->getPostulante;

            if (!$postulante) {
                return null;
            }

            $ingreso = $postulante->ingreso ?? 0;
            $edad = $postulante->birthdate
                ? \Carbon\Carbon::parse($postulante->birthdate)->age
                : 0;

            return [
                'first_name' => $postulante->first_name ?? '',
                'last_name' => $postulante->last_name ?? '',
                'cedula' => $postulante->cedula ?? '',
                'edad' => $edad,
                'ingreso' => $ingreso,
                'nivel' => ProjectHasPostulantes::calcularNivel($ingreso),
            ];
        })->filter();

        $contar = $postulantesData->count();

        $pdf = PDF::loadView('postulantesPDF', compact('project', 'postulantesData', 'contar'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('Listadopostulantes.pdf');
    }


    public function upload(Request $request)
    {

        $input['file_path'] = time().'.'.$request->image->getClientOriginalExtension();
        $request->image->move(public_path('images/'.$request->project_id.'/project/general'), $input['file_path']);

        $title = Document::find($request->title);
        //return $title->name;
        $input['title'] = $title->name;
        $input['postulante_id'] = $request->postulante_id;
        $input['document_id'] = $request->title;
        PostulantesDocuments::create($input);

        //return $input;

    	return back()
            ->with('success', 'Se ha agregado un Archivo!');
    }

    public function destroy(Request $request)
    {


        $postulante = ProjectHasPostulantes::where('postulante_id',$request->delete_id)->first();


        if ($postulante->getMembers->count() > 0) {
            // return back()->with('error', 'Debe eliminar todos los miembros antes de eliminar el postulante!');
            return back()->with('status', 'Debe eliminar todos los miembros antes de eliminar el postulante!');
        }else{

        ProjectHasPostulantes::where('postulante_id',$request->delete_id)->delete();
        PostulanteHasDiscapacidad::where('postulante_id',$request->delete_id)->delete();
        Postulante::find($request->delete_id)->delete();

        // return back()->with('error', 'Se ha eliminado el Postulante!');
        return back()->with('status', 'Se ha eliminado el Postulante!');
        }

    }


    public function destroymiembro(Request $request)
    {
        PostulanteHasBeneficiary::where('miembro_id',$request->delete_idmiembro)->delete();
        PostulanteHasDiscapacidad::where('postulante_id',$request->delete_idmiembro)->delete();
        Postulante::find($request->delete_idmiembro)->delete();
        return back()->with('error', 'Se ha eliminado el Miembro!');
    }

    public function destroyfile(Request $request)
    {

        $file = PostulantesDocuments::find($request->delete_id);

        $file_path = $this->photos_path . '/' . $file->project_id . '/project/general/' . $file->file_path;
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        PostulantesDocuments::find($request->delete_id)->delete();
        return back()->with('error', 'Se ha eliminado el archivo!');
    }

}
