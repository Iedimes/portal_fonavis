<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProjectStatus\BulkDestroyProjectStatus;
use App\Http\Requests\Admin\ProjectStatus\DestroyProjectStatus;
use App\Http\Requests\Admin\ProjectStatus\IndexProjectStatus;
use App\Http\Requests\Admin\ProjectStatus\StoreProjectStatus;
use App\Http\Requests\Admin\ProjectStatus\UpdateProjectStatus;
use App\Models\ProjectStatus;
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

class ProjectStatusController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexProjectStatus $request
     * @return array|Factory|View
     */
    public function index(IndexProjectStatus $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(ProjectStatus::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'project_id', 'stage_id', 'user_id', 'record'],

            // set columns to searchIn
            ['id', 'record']
        );

        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.project-status.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.project-status.create');

        return view('admin.project-status.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreProjectStatus $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreProjectStatus $request)
    {
        // Sanitize input
        //return $request;
        $sanitized = $request->getSanitized();
        $sanitized ['stage_id']=  $request->getStageId();
        // Store the ProjectStatus

        //return $sanitized;
        $projectStatus = ProjectStatus::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/projects/'. $request['project_id'] .'/show'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/project-statuses');
    }

    /**
     * Display the specified resource.
     *
     * @param ProjectStatus $projectStatus
     * @throws AuthorizationException
     * @return void
     */
    public function show(ProjectStatus $projectStatus)
    {
        $this->authorize('admin.project-status.show', $projectStatus);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param ProjectStatus $projectStatus
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(ProjectStatus $projectStatus)
    {
        $this->authorize('admin.project-status.edit', $projectStatus);


        return view('admin.project-status.edit', [
            'projectStatus' => $projectStatus,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateProjectStatus $request
     * @param ProjectStatus $projectStatus
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateProjectStatus $request, ProjectStatus $projectStatus)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values ProjectStatus
        $projectStatus->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/project-statuses'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/project-statuses');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyProjectStatus $request
     * @param ProjectStatus $projectStatus
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyProjectStatus $request, ProjectStatus $projectStatus)
    {
        $projectStatus->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyProjectStatus $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyProjectStatus $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    ProjectStatus::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}