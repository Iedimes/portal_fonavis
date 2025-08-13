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
use App\Models\ProjectOld;
use App\Models\ProjectHasPostulantes;
use App\Models\PostulanteHasBeneficiary;
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

        // Primero busca en Project
        $proyecto = Project::find($project_id);

        // Si no encuentra, busca en ProjectOld por project_id
        if (!$proyecto) {
           $proyecto = ProjectOld::where('project_id', $project_id)->first();
        }

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

        $projectId = $request->input('project_id');

        DB::beginTransaction();

        try {
            // Buscar primero en Project
            $project = Project::find($projectId);

            // Si no existe en Project, buscar en ProjectOld por project_id
            $isOld = false;
            if (!$project) {
                $project = ProjectOld::where('project_id', $projectId)->first();
                if ($project) {
                    $isOld = true;
                }
            }

            // Si no existe en ninguna, lanzamos excepción
            if (!$project) {
                throw new \Exception("Proyecto no encontrado");
            }

            // 1. Obtener los titulares del proyecto
            $titularIds = ProjectHasPostulantes::where('project_id', $projectId)
                            ->pluck('postulante_id');

            // 2. Obtener los miembro_id asociados a esos titulares
            $miembroIds = PostulanteHasBeneficiary::whereIn('postulante_id', $titularIds)
                            ->pluck('miembro_id');

            // 3. Eliminar físicamente los registros en postulante_has_beneficiaries
            PostulanteHasBeneficiary::whereIn('postulante_id', $titularIds)->delete();

            // 4. Borrado lógico de los miembros (grupo familiar)
            Postulante::whereIn('id', $miembroIds)->delete();

            // 5. Borrado lógico de los titulares
            Postulante::whereIn('id', $titularIds)->delete();

            // 6. Eliminar las relaciones en project_has_postulantes
            ProjectHasPostulantes::where('project_id', $projectId)->delete();

            // 7. Borrado lógico del proyecto
            $project->delete();

            DB::commit();

            $redirectUrl = $isOld ? url('admin/project-olds') : url('admin/projects');

            if ($request->ajax()) {
                return [
                    'redirect' => $redirectUrl,
                    'message' => trans('brackets/admin-ui::admin.operation.succeeded')
                ];
            }

            return redirect($redirectUrl);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('admin/projects')->withErrors([
                'error' => 'No se pudo dar de baja el proyecto y sus postulantes'
            ]);
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
