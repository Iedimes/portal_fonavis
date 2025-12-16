<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Postulante\BulkDestroyPostulante;
use App\Http\Requests\Admin\Postulante\DestroyPostulante;
use App\Http\Requests\Admin\Postulante\IndexPostulante;
use App\Http\Requests\Admin\Postulante\StorePostulante;
use App\Http\Requests\Admin\Postulante\UpdatePostulante;
use App\Models\Postulante;
use App\Models\Project;
use App\Models\ProjectHasPostulantes;
use App\Models\PostulanteHasBeneficiary;
use App\Models\PostulanteHasDiscapacidad;
use App\Models\SIG005;
use App\Models\SIG006;
use App\Models\Usuario;
use PDF;
use Brackets\AdminListing\Facades\AdminListing;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Exports\PostulantesExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class PostulantesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexPostulante $request
     * @return array|Factory|View
     */
    public function index(IndexPostulante $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(Postulante::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'first_name', 'last_name', 'cedula', 'marital_status', 'nacionalidad', 'gender', 'birthdate', 'localidad', 'asentamiento', 'ingreso', 'address', 'grupo', 'phone', 'mobile', 'nexp'],

            // set columns to searchIn
            ['id', 'first_name', 'last_name', 'cedula', 'marital_status', 'nacionalidad', 'gender', 'birthdate', 'localidad', 'asentamiento', 'address', 'grupo', 'phone', 'mobile', 'nexp'],

            // modifyQuery callback
            function($query) {
                return $query->withRelationsOptimized();
            }
        );

        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.postulante.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.postulante.create');

        return view('admin.postulante.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StorePostulante $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StorePostulante $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the Postulante
        $postulante = Postulante::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/postulantes'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/postulantes');
    }

    /**
     * Display the specified resource.
     *
     * @param Postulante $postulante
     * @throws AuthorizationException
     * @return void
     */
    public function show(Postulante $postulante)
    {
        $this->authorize('admin.postulante.show', $postulante);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Postulante $postulante
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(Postulante $postulante)
    {
        $this->authorize('admin.postulante.edit', $postulante);


        return view('admin.postulante.edit', [
            'postulante' => $postulante,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdatePostulante $request
     * @param Postulante $postulante
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdatePostulante $request, Postulante $postulante)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values Postulante
        $postulante->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/postulantes'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/postulantes');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyPostulante $request
     * @param Postulante $postulante
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    // public function destroy(DestroyPostulante $request, Postulante $postulante)
    // {
    //     return $request;
    //     $postulante->delete();

    //     if ($request->ajax()) {
    //         return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    //     }

    //     return redirect()->back();
    // }
    public function destroyMiembro(Request $request)
    {
        $postulante = Postulante::find($request->delete_id);

        if (!$postulante) {
            return back()->with('error', 'Postulante no encontrado');
        }

        // Verificar si es un TITULAR
        $isTitular = ProjectHasPostulantes::where('postulante_id', $postulante->id)->exists();

        if ($isTitular) {
            // Si es TITULAR: Eliminar grupo familiar completo

            // 1. Obtener todos los miembros del titular
            $miembros = PostulanteHasBeneficiary::where('postulante_id', $postulante->id)->pluck('miembro_id');

            // 2. Eliminar miembros y sus relaciones
            foreach ($miembros as $miembroId) {
                PostulanteHasDiscapacidad::where('postulante_id', $miembroId)->delete();
                PostulanteHasBeneficiary::where('miembro_id', $miembroId)->delete();
                Postulante::find($miembroId)->delete();
            }

            // 3. Eliminar relaciones del titular
            ProjectHasPostulantes::where('postulante_id', $postulante->id)->delete();
            PostulanteHasDiscapacidad::where('postulante_id', $postulante->id)->delete();
            PostulanteHasBeneficiary::where('miembro_id', $postulante->id)->delete();

            // 4. Eliminar el titular
            $postulante->delete();

            return back()->with('status', 'Se ha eliminado el titular y su grupo familiar!');
        } else {
            // Si es MIEMBRO: Eliminar solo el miembro

            PostulanteHasDiscapacidad::where('postulante_id', $postulante->id)->delete();
            PostulanteHasBeneficiary::where('miembro_id', $postulante->id)->delete();
            $postulante->delete();

            return back()->with('status', 'Se ha eliminado el miembro del grupo familiar!');
        }
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyPostulante $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyPostulante $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    Postulante::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }


    public function guardar(StorePostulante $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the Postulante
        $postulante = Postulante::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/postulantes'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/postulantes');
    }

    public function imprimir($id)
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

    public function actualizar(Request $request, $id)
    {
        $user = Auth::user();
        $email = $user->email;
        $username = strtoupper(explode('@', $email)[0]);

        $usuario = Usuario::where('UsuCod', $username)->first();

        $dependencia = $usuario->DepenCod;
        $nombreusuario = $usuario->UsuNombre;

        // Validar la solicitud
        $request->validate([
            'field' => 'required|string',
            // 'value' => 'required|string',
        ]);

        // Buscar el postulante
        $postulante = Postulante::findOrFail($id);

        // Actualizar el campo correspondiente
        $postulante->{$request->field} = $request->value;
        $postulante->save();

        // Inicializar variables para usar en ambos bloques
        $sig005 = SIG005::where('NroExpPer', $postulante->cedula)
                        ->where('TexCod', 118)
                        ->first();
        if ($request->field === 'califica' && $request->value === 'N') {
            if ($sig005) {
                // Obtener el NroExp
                // return "No califica";
                $nroExp = $sig005->NroExp;
                $detalle = SIG006::where('NroExp', $nroExp)
                                    ->orderBy('DENroLin', 'desc')
                                    ->first();
                $nroLin = $detalle->DENroLin + 1;
                $date = new \DateTime();

                // Insertar un nuevo registro en SIG006 usando el modelo
                Sig006::create([
                    'NroExp' => $nroExp,
                    'NroExpS' => 'A',
                    'DENroLin' => $nroLin,
                    'DEExpEst' => 'N', // Estado 'N'
                    'DEFecDis' => date_format($date, 'Ymd H:i:s'),
                    'UsuRcp' => $username,
                    'DEUnOrHa' => $dependencia,
                    'DEUnOrDe' => $dependencia,
                    'DERcpChk' => 1,
                    'DERcpNam' => $nombreusuario,
                    'DEExpAcc' => $postulante->observacion_de_consideracion,
                    // Agrega aquí cualquier otro campo requerido por tu tabla SIG006
                ]);
            }
        } else
        if ($request->field === 'califica' && $request->value === 'S') {
            // Si no es N, entonces es un estado 'P'
            if ($sig005) {
                $nroExp = $sig005->NroExp;
                $detalle = SIG006::where('NroExp', $nroExp)
                                    ->orderBy('DENroLin', 'desc')
                                    ->first();
                $nroLin = $detalle ? $detalle->DENroLin + 1 : 1; // Manejar caso si no hay detalle
                $date = new \DateTime();

                // Insertar un nuevo registro en SIG006 usando el modelo
                Sig006::create([
                    'NroExp' => $nroExp,
                    'NroExpS' => 'A',
                    'DENroLin' => $nroLin,
                    'DEExpEst' => 'K', // Estado 'P'
                    'DEFecDis' => date_format($date, 'Ymd H:i:s'),
                    'UsuRcp' => $username,
                    'DEUnOrHa' => $dependencia,
                    'DEUnOrDe' => $dependencia,
                    'DERcpChk' => 1,
                    'DERcpNam' => $nombreusuario,
                    'DEExpAcc' => $postulante->observacion_de_consideracion,
                    // Agrega aquí cualquier otro campo requerido por tu tabla SIG006
                ]);
            }
        }

        // Retornar una respuesta
        return response()->json(['success' => true]);
    }

    public function exportar($projectId = null)
    {
        // Si no se proporciona projectId, usar el primer proyecto disponible o manejar según tu lógica
        if (!$projectId) {
            // Aquí puedes ajustar la lógica según cómo determines qué proyecto exportar
            $projectId = request()->get('project_id');
        }

        // $project = Project::with([
        //     'getState',
        //     'getCity',
        //     'getSat',
        //     'getModality',
        //     'getLand',
        //     'getTypology'
        // ])->findOrFail($projectId);

        $project = Project::findOrFail($projectId);

        $postulantes = ProjectHasPostulantes::with([
            'getPostulante',
            'getMembers.getPostulante'
        ])->where('project_id', $projectId)->get();

        return Excel::download(new PostulantesExport($project, $postulantes),
            'PLANILLA-' . str_replace(' ', '-', $project->id.'-CH') . '.xlsx');
    }

    public function guardarmiembro(Request $request)
    {
        // Validar los campos esenciales que vienen del formulario
        $request->validate([
            'cedula' => 'required|string|max:255',
            'parentesco_id' => 'required|integer|exists:parentesco,id',
            'birthdate' => 'required|date', // Valida 'birthdate'
            'discapacidad_id' => 'nullable|integer|exists:discapacidad,id',
            // Agrega aquí otras validaciones si es necesario para los campos del postulante
        ]);

        $parentescoId = $request->input('parentesco_id');
        $fechaNacimiento = $request->input('birthdate'); // Obtenemos 'birthdate' del request

        // --- INICIO DE LA SECCIÓN DEPURACIÓN ---
        \Log::info('storemiembro: Valor de parentesco_id: ' . $parentescoId);
        \Log::info('storemiembro: Valor de fechaNacimiento (birthdate): ' . $fechaNacimiento);
        // --- FIN DE LA SECCIÓN DEPURACIÓN ---

        // Aplicar la validación de edad solo para parentesco 1 (Cónyuge) y 8 (Postulante Titular)
        if (in_array($parentescoId, [1, 8])) {
            // Llamamos al método auxiliar para validar la edad
            $validacionEdad = $this->validarEdadMinima($fechaNacimiento);

            // --- INICIO DE LA SECCIÓN DEPURACIÓN ---
            \Log::info('storemiembro: Resultado de validarEdadMinima: ', $validacionEdad);
            // --- FIN DE LA SECCIÓN DEPURACIÓN ---

            // Si la validación de edad falla, redirigimos con un error
            if (!$validacionEdad['esValido']) {
                // Se redirige directamente al listado general, no se necesita withInput() aquí
                return redirect('admin/projects/' . $request->project_id . '/showDGSO')
                                 ->with('error', $validacionEdad['mensaje']);
            }
        }

        // Si la validación de edad pasa (o no aplica), procedemos a guardar los datos

        // Excluimos campos que no van directamente a la tabla 'postulantes'
        // IMPORTANTE: 'birthdate' ya NO se excluye porque es una columna directa en la tabla 'postulantes'
        $input = $request->except(['_token', 'project_id', 'discapacidad_id', 'postulante_id']);

        // No se necesita $input['fecha'] = $request->input('birthdate');
        // porque 'birthdate' ya está incluido en $input si la columna en la BD se llama 'birthdate'.
        // Si tu columna en la BD se llama 'fecha', entonces deberías cambiar el nombre del campo en el formulario a 'fecha'
        // o asegurarte de que 'fecha' se mapee correctamente aquí.
        // Dado el error, asumimos que la columna es 'birthdate'.

        // Crear el nuevo postulante (miembro familiar)
        $postulante = Postulante::create($input);

        // Guardar la relación entre el postulante principal y el miembro familiar
        $miembro = new PostulanteHasBeneficiary();
        $miembro->postulante_id = $request->postulante_id; // ID del postulante principal
        $miembro->miembro_id = $postulante->id; // ID del nuevo miembro familiar
        $miembro->parentesco_id = $request->parentesco_id;
        $miembro->save();

        // Guardar la relación de discapacidad si se seleccionó una
        if ($request->filled('discapacidad_id')) {
            $postulantediscapacidad = new PostulanteHasDiscapacidad();
            $postulantediscapacidad->discapacidad_id = $request->discapacidad_id;
            $postulantediscapacidad->postulante_id = $postulante->id;
            $postulantediscapacidad->save();
        }

        // Redirigir a la vista de postulantes del proyecto con un mensaje de éxito
        return redirect('admin/projects/' . $request->project_id . '/showDGSO')->with('success', 'Se ha agregado un nuevo Miembro!');
    }

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

}
