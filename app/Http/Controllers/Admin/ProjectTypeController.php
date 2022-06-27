<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProjectType\BulkDestroyProjectType;
use App\Http\Requests\Admin\ProjectType\DestroyProjectType;
use App\Http\Requests\Admin\ProjectType\IndexProjectType;
use App\Http\Requests\Admin\ProjectType\StoreProjectType;
use App\Http\Requests\Admin\ProjectType\UpdateProjectType;
use App\Models\ProjectType;
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

class ProjectTypeController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexProjectType $request
     * @return array|Factory|View
     */
    public function index(IndexProjectType $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(ProjectType::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'name', 'short_name'],

            // set columns to searchIn
            ['id', 'name', 'short_name']
        );

        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.project-type.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.project-type.create');

        return view('admin.project-type.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreProjectType $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreProjectType $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the ProjectType
        $projectType = ProjectType::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/project-types'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/project-types');
    }

    /**
     * Display the specified resource.
     *
     * @param ProjectType $projectType
     * @throws AuthorizationException
     * @return void
     */
    public function show(ProjectType $projectType)
    {
        $this->authorize('admin.project-type.show', $projectType);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param ProjectType $projectType
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(ProjectType $projectType)
    {
        $this->authorize('admin.project-type.edit', $projectType);


        return view('admin.project-type.edit', [
            'projectType' => $projectType,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateProjectType $request
     * @param ProjectType $projectType
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateProjectType $request, ProjectType $projectType)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values ProjectType
        $projectType->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/project-types'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/project-types');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyProjectType $request
     * @param ProjectType $projectType
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyProjectType $request, ProjectType $projectType)
    {
        $projectType->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyProjectType $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyProjectType $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    ProjectType::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
