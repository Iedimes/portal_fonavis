<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Reporte\BulkDestroyReporte;
use App\Http\Requests\Admin\Reporte\DestroyReporte;
use App\Http\Requests\Admin\Reporte\IndexReporte;
use App\Http\Requests\Admin\Reporte\StoreReporte;
use App\Http\Requests\Admin\Reporte\UpdateReporte;
use App\Models\Reporte;
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
use App\Models\Project;
use App\Models\Departamento;
use App\Models\Distrito;
use App\Models\Sat;
use App\Models\Modality;
use App\Models\Stage;


class ReporteController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexReporte $request
     * @return array|Factory|View
     */
    public function index(IndexReporte $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(Reporte::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'inicio', 'fin', 'sat_id', 'state_id', 'city_id', 'modalidad_id', 'stage_id'],

            // set columns to searchIn
            ['id', 'sat_id', 'city_id']
        );

        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.reporte.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.reporte.create');

        $proyecto = Project::where('created_at','>=', '2024-07-13')
                            ->OrWhere('updated_at', '>=', '2024-07-13')
                            ->get();

        $sat = Sat::where('NucRuc','!=', null)
        ->where('NucEst','=', 'H')
        ->select('NucNomSat','NucCod','NucCont')
        ->get();

        $dep = [18, 19, 20, 21, 999];
        $departamento = Departamento::whereNotIn('DptoId', $dep)
                          ->orderBy('DptoNom', 'asc')->get();
        
        $dis = [0, 900, 998];
        $distrito = Distrito::whereNotIn('CiuId', $dis)
            ->orderBy('CiuNom', 'asc')->get();

        $modalidad = Modality::all();

        $estado=Stage::all();

        return view('admin.reporte.create', compact('proyecto', 'sat', 'departamento', 'distrito', 'modalidad', 'estado'));
    }

    public function getCities(Request $request)
{
    $stateId = $request->query('state_id');
    $cities = Distrito::where('CiuDptoID', $stateId)->get(); // Adjust the query based on your database structure
    return response()->json($cities);
}
    /**
     * Store a newly created resource in storage.
     *
     * @param StoreReporte $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreReporte $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the Reporte
        $reporte = Reporte::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/reportes'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/reportes');
    }

    public function resultados(Request $request)
    {
        // return $request;
   // Reglas de validación
   $rules = [
        'inicio' => 'nullable|date',
        'fin' => 'nullable|date',
        'proyecto_id' => 'nullable|integer',
        'sat_id' => 'nullable|integer',
        'state_id' => 'nullable|integer',
        'city_id' => 'nullable|integer',
        'modalidad_id' => 'nullable|integer',
        'stage_id' => 'nullable|integer',
    ];
    $messages = [
        // 'inicio.required' => 'Debe cargar la fecha de inicio.',
        // 'fin.required' => 'Debe cargar la fecha de fin.',
        // 'proyecto_id.required' => 'Debe seleccionar un proyecto.',
        // 'sat_id.required' => 'Debe seleccionar un SAT.',
        // 'state_id.required' => 'Debe seleccionar un departamento.',
        // 'city_id.required' => 'Debe seleccionar una ciudad.',
        // 'modalidad_id.required' => 'Debe seleccionar una modalidad.',
        // 'stage_id.required' => 'Debe seleccionar un estado.',
        'inicio.required_with' => 'Debe proporcionar una fecha de inicio cuando se especifica una fecha de fin.',
        'fin.required_with' => 'Debe proporcionar una fecha de fin cuando se especifica una fecha de inicio.',
        'fin.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
        'integer' => 'El campo :attribute debe ser un número entero.',

    ];
    // $this->validate($request, $rules, $messages);

     // Validar la solicitud
     $validatedData = $request->validate($rules, $messages);

     // Obtener datos validados
     $inicio = $validatedData['inicio'] ?? null;
     $fin = $validatedData['fin'] ?? null;
     $proyecto_id = $validatedData['proyecto_id'] ?? null;
     $sat_id = $validatedData['sat_id'] ?? null;
     $state_id = $validatedData['state_id'] ?? null;
     $city_id = $validatedData['city_id'] ?? null;
     $modalidad_id = $validatedData['modalidad_id'] ?? null;
     $stage_id = $validatedData['stage_id'] ?? null;

    // // Obtener datos del request
    // $inicio = $request->inicio;
    // $fin = $request->fin;
    // $proyecto_id = $request->proyecto_id;
    // $sat_id = $request->sat_id;
    // $state_id = $request->state_id;
    // $city_id = $request->city_id;
    // $modalidad_id = $request->modalidad_id;
    // $stage_id = $request->stage_id;

    // Inicializar la consulta
   // Definir la fecha de referencia
    $fechaReferencia = '2024-07-13';

    // Construir la consulta inicial
    $query = Project::where(function ($q) use ($fechaReferencia) {
        $q->where('created_at', '>=', $fechaReferencia)
        ->orWhere('updated_at', '>=', $fechaReferencia);
    });

    if (!empty($inicio) && !empty($fin)) {
        // Aplicar filtros adicionales a la consulta
        $query->whereBetween('created_at', [$inicio, $fin])
            ->orWhereBetween('updated_at', [$inicio, $fin]);
    }

    if ($proyecto_id > 0) {
        $query->where('id', $proyecto_id);
    }

    if ($sat_id > 0) {
        $query->where('sat_id', $sat_id);
    }

    if ($state_id > 0) {
        $query->where('id', $state_id);
    }

    if ($city_id > 0) {
        $query->where('city_id', $city_id);
    }

    if ($modalidad_id > 0) {
        $query->where('id', $modalidad_id);
    }

    if ($stage_id > 0) {
        $query->where('id', $stage_id);
    }



    return $results = $query->get();

    // Retornar los resultados a la vista correspondiente
    return view('resultados', compact('results'));
    
}

    /**
     * Display the specified resource.
     *
     * @param Reporte $reporte
     * @throws AuthorizationException
     * @return void
     */
    public function show(Reporte $reporte)
    {
        $this->authorize('admin.reporte.show', $reporte);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Reporte $reporte
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(Reporte $reporte)
    {
        $this->authorize('admin.reporte.edit', $reporte);


        return view('admin.reporte.edit', [
            'reporte' => $reporte,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateReporte $request
     * @param Reporte $reporte
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateReporte $request, Reporte $reporte)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values Reporte
        $reporte->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/reportes'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/reportes');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyReporte $request
     * @param Reporte $reporte
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyReporte $request, Reporte $reporte)
    {
        $reporte->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyReporte $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyReporte $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    Reporte::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
