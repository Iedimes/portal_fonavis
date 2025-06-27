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
        $title="Lista de Postulantes";
        $project = Project::find($id);
        $postulantes = ProjectHasPostulantes::where('project_id',$id)->get();

        //return $postulantes;
        //Mapper::map(-24.3697635, -56.5912129, ['zoom' => 6, 'type' => 'ROADMAP']);
        return view('postulantes.index',compact('project','title','postulantes'));
        //return "hola";
    }

    public function create(Request $request, $id)
    {
        if (!$request->filled('cedula')) {
            return redirect()->back()->with('error', 'Ingrese Cédula');
        }

        $cedula = $request->input('cedula');

        // Verificación previa de registros
        $mensajes = $this->verificarRestricciones($cedula);
        if ($mensajes) {
            return redirect()->back()->with('status', $mensajes);
        }

        // Intentar obtener datos desde la API o base local
        $datos = $this->obtenerDatosPersona($cedula);
        if (!$datos) {
            return redirect()->back()->with('status', 'No se pudieron recuperar los datos desde el servicio ni desde la base local.');
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


    private function verificarRestricciones($cedula)
    {
        $expediente = SIG005::where('NroExpPer', $cedula)
                        ->where('TexCod', 118)
                        ->orderBy('NroExp', 'desc')
                        ->first();

        if ($expediente) {
            $archivo = SIG006::where('NroExp', $expediente->NroExp)
                            ->whereIn('DEExpEst', ['C', 'H'])
                            ->first();
            if (!$archivo) return null;
        }

        $mensajes = [
            Postulante::where('cedula', $cedula)->exists() => 'Ya existe el postulante!',
            SHMCER::where('CerPosCod', $cedula)->whereNotIn('CerEst', [2, 7, 8, 12])->exists() => 'Ya cuenta con certificado de Subsidio como Titular!',
            SHMCER::where('CerCoCI', $cedula)->whereNotIn('CerEst', [2, 7, 8, 12])->exists() => 'Ya cuenta con certificado de Subsidio como Conyuge!',
            PRMCLI::where('PerCod', $cedula)->where('PylCod', '!=', 'P.F.')->exists() => 'Ya cuenta con Beneficios en la Institución!',
            IVMSOL::where('SolPerCod', $cedula)->where('SolEtapa', 'B')->exists() => 'Ya es Beneficiario Final!'
        ];

        $solicitante = IVMSOL::where('SolPerCge', $cedula)->first();
        if ($solicitante) {
            $carterasol = PRMCLI::where('PerCod', trim($solicitante->SolPerCod))
                                ->where('PylCod', '!=', 'P.F.')
                                ->exists();
            if ($carterasol) {
                return 'Ya cuenta con Beneficios en la Institución como Conyuge!';
            }
        }

        foreach ($mensajes as $cond => $msg) {
            if ($cond) return $msg;
        }

        return null;
    }

    private function obtenerDatosPersona($cedula)
    {
        try {
            $client = new \GuzzleHttp\Client();

            $auth = $client->post('http://192.168.195.1:8080/mbohape-core/sii/security', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
                'json' => [
                    'username' => 'senavitatconsultas',
                    'password' => 'S3n4vitat'
                ]
            ]);

            $tokenData = json_decode($auth->getBody()->getContents());

            if (!$tokenData->success) throw new \Exception("API sin éxito");

            $response = $client->get("http://192.168.195.1:8080/frontend-identificaciones/api/persona/obtenerPersonaPorCedula/{$cedula}", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $tokenData->token,
                    'Accept' => 'application/json'
                ]
            ]);

            $persona = json_decode($response->getBody()->getContents());
            $p = $persona->obtenerPersonaPorNroCedulaResponse->return ?? null;

            if (!$p || isset($p->error)) return null;

            return [
                'nombre' => $p->nombres,
                'apellido' => $p->apellido,
                'cedula' => $p->cedula,
                'sexo' => $p->sexo,
                'fecha' => date('Y-m-d H:i:s', strtotime($p->fechNacim)),
                'nac' => $p->nacionalidadBean ?? '',
                'est' => $p->estadoCivil ?? ''
            ];

        } catch (\Exception $e) {
            $persona = \App\Models\Persona::where('BDICed', $cedula)->first();

            if (!$persona) return null;

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
        if ($msg = $this->verificarRestriccionesMiembro($cedula)) {
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


    private function verificarRestriccionesMiembro($cedula)
    {
        $expediente = SIG005::where('NroExpPer', $cedula)->where('TexCod', 118)->orderBy('NroExp', 'desc')->first();

        if ($expediente) {
            $archivo = SIG006::where('NroExp', $expediente->NroExp)
                ->whereIn('DEExpEst', ['C', 'H'])
                ->first();

            if (!$archivo) return null;
        }

        if (Postulante::where('cedula', $cedula)->exists()) return 'Ya existe el postulante!';

        if (SHMCER::where('CerPosCod', $cedula)->whereNotIn('CerEst', [2, 7, 8, 12])->exists()) {
            return 'Ya cuenta con certificado de Subsidio como Titular!';
        }

        if (SHMCER::where('CerCoCI', $cedula)->whereNotIn('CerEst', [2, 7, 8, 12])->exists()) {
            return 'Ya cuenta con certificado de Subsidio como Conyuge!';
        }

        if (PRMCLI::where('PerCod', $cedula)->where('PylCod', '!=', 'P.F.')->exists()) {
            return 'Ya cuenta con Beneficios en la Institución!';
        }

        $solicitante = IVMSOL::where('SolPerCge', $cedula)->first();
        if ($solicitante && PRMCLI::where('PerCod', trim($solicitante->SolPerCod))->where('PylCod', '!=', 'P.F.')->exists()) {
            return 'Ya cuenta con Beneficios en la Institución como Conyuge!';
        }

        if (IVMSOL::where('SolPerCod', $cedula)->where('SolEtapa', 'B')->exists()) {
            return 'Ya es Beneficiario Final!';
        }

        return null;
    }

    public function store(StorePostulante $request)
    {


        //return $request;

        $input = $request->except(['_token','project_id','discapacidad_id']);
        $postulante = Postulante ::create($input);

        $proypostulante = new ProjectHasPostulantes();
        $proypostulante->project_id=$request->project_id;
        $proypostulante->postulante_id=$postulante->id;
        $proypostulante->save();



        $postulantediscapacidad = new PostulanteHasDiscapacidad();
        $postulantediscapacidad->discapacidad_id=$request->discapacidad_id;
        $postulantediscapacidad->postulante_id=$postulante->id;
        $postulantediscapacidad->save();
        //ProjectHasPostulantes::

        //return $request->all();
        //Postulante ::create($request->all());
        return redirect('projects/'.$request->project_id.'/postulantes')->with('success', 'Se ha agregado un nuevo Postulante!');
        //return $request;
    }

    public function storeEditPostulante(StorePostulante $request)
    {
        // Encuentra el postulante por ID
        $postulante = Postulante::findOrFail($request->postulante_id);

        // Rellena los atributos del postulante con los datos validados
        $postulante->fill($request->all());

        // Guarda los cambios
        $postulante->save();

        // Redirige con un mensaje de éxito
        return redirect('projects/'.$request->project_id.'/postulantes')->with('success', 'Se ha editado correctamente el Postulante!');
    }

    public function storemiembro(Request $request)
    {
        //return $request;
        $input = $request->except(['_token','project_id','discapacidad_id','postulante_id']);
        $postulante = Postulante ::create($input);

        $miembro = new PostulanteHasBeneficiary();
        $miembro->postulante_id=$request->postulante_id;
        $miembro->miembro_id=$postulante->id;
        $miembro->parentesco_id=$request->parentesco_id;
        $miembro->save();

        $postulantediscapacidad = new PostulanteHasDiscapacidad();
        $postulantediscapacidad->discapacidad_id=$request->discapacidad_id;
        $postulantediscapacidad->postulante_id=$postulante->id;
        $postulantediscapacidad->save();
        //ProjectHasPostulantes::

        //return $request->all();
        //Postulante ::create($request->all());
        //return redirect('projects/'.$request->project_id.'/postulantes/'.$request->postulante_id)->with('success', 'Se ha agregado un nuevo Miembro!');
        return redirect('projects/'.$request->project_id.'/postulantes')->with('success', 'Se ha agregado un nuevo Miembro!');
        //return $request;
    }

    public function storemiembroeditar(Request $request){
       // return "Guardar Edicion";
        $postulante = Postulante::findOrFail($request->postulante_id);
        $postulante->fill($request->all());
        $postulante->save();
        return redirect('projects/'.$request->project_id.'/postulantes/'.$request->postulante_id)->with('success', 'Se ha editado correctamente el Miembro!');
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



    public function editmiembro($id,$idpostulante)
    {
        $title="Editar Miembro";
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
        $estado= ProjectStatus::where('project_id', $id)->first();
        if (empty($estado)){
            //return "Vacio";
            $par = [1,8];
            $parentesco = Parentesco::whereIn('id', $par)->get();
        }else{
            $parentesco = Parentesco::all();
        }

        $parent = PostulanteHasBeneficiary::where('miembro_id',$postulante->id)->first();
        $idpostulante=$parent->postulante_id;
        $idmiembro=$parent->miembro_id;

        return view('postulantes.ficha.editmiembro',compact('title','project','postulante','apellido','cedula','sexo','project_id',
                                                'nombre','nac','est','fecha','discapacdad','disc','parentesco','idpostulante', 'idmiembro'));
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

    public function generatePDF($id)
    {
        $project=Project::find($id);
        $postulantes = ProjectHasPostulantes::where('project_id',$id)->get();
        $contar = count($postulantes);
        $pdf = PDF::loadView('postulantesPDF', compact('project','postulantes', 'contar'))->setPaper('a4', 'landscape');

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
