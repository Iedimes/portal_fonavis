<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProjectOld\BulkDestroyProjectOld;
use App\Http\Requests\Admin\ProjectOld\DestroyProjectOld;
use App\Http\Requests\Admin\ProjectOld\IndexProjectOld;
use App\Http\Requests\Admin\ProjectOld\StoreProjectOld;
use App\Http\Requests\Admin\ProjectOld\UpdateProjectOld;
use App\Models\ProjectOld;
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

class ProjectOldsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexProjectOld $request
     * @return array|Factory|View
     */
    public function index(IndexProjectOld $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(ProjectOld::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'project_id', 'name'],

            // set columns to searchIn
            ['id', 'name']
        );

        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.project-old.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.project-old.create');

        return view('admin.project-old.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreProjectOld $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreProjectOld $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the ProjectOld
        $projectOld = ProjectOld::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/project-olds'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/project-olds');
    }

    /**
     * Display the specified resource.
     *
     * @param ProjectOld $projectOld
     * @throws AuthorizationException
     * @return void
     */
    public function show(ProjectOld $projectOld)
    {
        $this->authorize('admin.project-old.show', $projectOld);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param ProjectOld $projectOld
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(ProjectOld $projectOld)
    {
        $this->authorize('admin.project-old.edit', $projectOld);


        return view('admin.project-old.edit', [
            'projectOld' => $projectOld,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateProjectOld $request
     * @param ProjectOld $projectOld
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateProjectOld $request, ProjectOld $projectOld)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values ProjectOld
        $projectOld->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/project-olds'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/project-olds');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyProjectOld $request
     * @param ProjectOld $projectOld
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyProjectOld $request, ProjectOld $projectOld)
    {
        $projectOld->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyProjectOld $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyProjectOld $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    ProjectOld::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
