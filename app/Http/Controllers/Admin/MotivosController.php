<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Motivo\BulkDestroyMotivo;
use App\Http\Requests\Admin\Motivo\DestroyMotivo;
use App\Http\Requests\Admin\Motivo\IndexMotivo;
use App\Http\Requests\Admin\Motivo\StoreMotivo;
use App\Http\Requests\Admin\Motivo\UpdateMotivo;
use App\Models\Motivo;
use App\Models\Project;
use App\Models\ProjectHasPostulantes;
use App\Models\Postulante;
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

class MotivosController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexMotivo $request
     * @return array|Factory|View
     */
    public function index(IndexMotivo $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(Motivo::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'project_id', 'motivo'],

            // set columns to searchIn
            ['id', 'motivo']
        );

        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.motivo.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create($project_id)
    {
        $this->authorize('admin.motivo.create');

        $proyecto = Project::where('id', $project_id)->first();

        return view('admin.motivo.create', compact('proyecto', 'project_id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreMotivo $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreMotivo $request)
{
    // Sanitize input
    $sanitized = $request->getSanitized();

    // Store the Motivo
    $motivo = Motivo::create($sanitized);

    // ID del proyecto a dar de baja
    $projectId = $request->input('project_id'); // suponiendo que llega el ID en el request

    // Iniciar transacción para asegurar que todas las operaciones se completen correctamente
    DB::beginTransaction();

    try {
        // Dar de baja el proyecto
        $project = Project::findOrFail($projectId);

        // Obtener los IDs de los postulantes asociados al proyecto
        $postulanteIds = ProjectHasPostulantes::where('project_id', $projectId)
                         ->pluck('postulante_id');

        // Dar de baja los postulantes en la tabla `postulantes`
        Postulante::whereIn('id', $postulanteIds)->delete();

        // Eliminar las relaciones en la tabla `project_has_postulantes`
        ProjectHasPostulantes::where('project_id', $projectId)->delete();

        // Finalmente, eliminar el proyecto
        $project->delete();

        // Confirmar transacción
        DB::commit();

        // Redirección o respuesta AJAX
        if ($request->ajax()) {
            return [
                'redirect' => url('admin/projects'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded')
            ];
        }

        return redirect('admin/projects');

    } catch (\Exception $e) {
        // Revertir transacción en caso de error
        DB::rollBack();
        return redirect('admin/projects')->withErrors(['error' => 'No se pudo dar de baja el proyecto y sus postulantes']);
    }
}





    /**
     * Display the specified resource.
     *
     * @param Motivo $motivo
     * @throws AuthorizationException
     * @return void
     */
    public function show(Motivo $motivo)
    {
        $this->authorize('admin.motivo.show', $motivo);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Motivo $motivo
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(Motivo $motivo)
    {
        $this->authorize('admin.motivo.edit', $motivo);


        return view('admin.motivo.edit', [
            'motivo' => $motivo,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateMotivo $request
     * @param Motivo $motivo
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateMotivo $request, Motivo $motivo)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values Motivo
        $motivo->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/motivos'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/motivos');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyMotivo $request
     * @param Motivo $motivo
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyMotivo $request, Motivo $motivo)
    {
        $motivo->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyMotivo $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyMotivo $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    Motivo::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
