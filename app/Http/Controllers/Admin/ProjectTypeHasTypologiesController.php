<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProjectTypeHasTypology\BulkDestroyProjectTypeHasTypology;
use App\Http\Requests\Admin\ProjectTypeHasTypology\DestroyProjectTypeHasTypology;
use App\Http\Requests\Admin\ProjectTypeHasTypology\IndexProjectTypeHasTypology;
use App\Http\Requests\Admin\ProjectTypeHasTypology\StoreProjectTypeHasTypology;
use App\Http\Requests\Admin\ProjectTypeHasTypology\UpdateProjectTypeHasTypology;
use App\Models\ProjectTypeHasTypology;
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

class ProjectTypeHasTypologiesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexProjectTypeHasTypology $request
     * @return array|Factory|View
     */
    public function index(IndexProjectTypeHasTypology $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(ProjectTypeHasTypology::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'project_type_id', 'typology_id'],

            // set columns to searchIn
            ['id']
        );

        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.project-type-has-typology.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.project-type-has-typology.create');

        return view('admin.project-type-has-typology.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreProjectTypeHasTypology $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreProjectTypeHasTypology $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the ProjectTypeHasTypology
        $projectTypeHasTypology = ProjectTypeHasTypology::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/project-type-has-typologies'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/project-type-has-typologies');
    }

    /**
     * Display the specified resource.
     *
     * @param ProjectTypeHasTypology $projectTypeHasTypology
     * @throws AuthorizationException
     * @return void
     */
    public function show(ProjectTypeHasTypology $projectTypeHasTypology)
    {
        $this->authorize('admin.project-type-has-typology.show', $projectTypeHasTypology);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param ProjectTypeHasTypology $projectTypeHasTypology
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(ProjectTypeHasTypology $projectTypeHasTypology)
    {
        $this->authorize('admin.project-type-has-typology.edit', $projectTypeHasTypology);


        return view('admin.project-type-has-typology.edit', [
            'projectTypeHasTypology' => $projectTypeHasTypology,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateProjectTypeHasTypology $request
     * @param ProjectTypeHasTypology $projectTypeHasTypology
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateProjectTypeHasTypology $request, ProjectTypeHasTypology $projectTypeHasTypology)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values ProjectTypeHasTypology
        $projectTypeHasTypology->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/project-type-has-typologies'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/project-type-has-typologies');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyProjectTypeHasTypology $request
     * @param ProjectTypeHasTypology $projectTypeHasTypology
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyProjectTypeHasTypology $request, ProjectTypeHasTypology $projectTypeHasTypology)
    {
        $projectTypeHasTypology->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyProjectTypeHasTypology $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyProjectTypeHasTypology $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    ProjectTypeHasTypology::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
