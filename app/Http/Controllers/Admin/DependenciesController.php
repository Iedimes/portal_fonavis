<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Dependency\BulkDestroyDependency;
use App\Http\Requests\Admin\Dependency\DestroyDependency;
use App\Http\Requests\Admin\Dependency\IndexDependency;
use App\Http\Requests\Admin\Dependency\StoreDependency;
use App\Http\Requests\Admin\Dependency\UpdateDependency;
use App\Models\Dependency;
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

class DependenciesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexDependency $request
     * @return array|Factory|View
     */
    public function index(IndexDependency $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(Dependency::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'name'],

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

        return view('admin.dependency.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.dependency.create');

        return view('admin.dependency.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreDependency $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreDependency $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the Dependency
        $dependency = Dependency::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/dependencies'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/dependencies');
    }

    /**
     * Display the specified resource.
     *
     * @param Dependency $dependency
     * @throws AuthorizationException
     * @return void
     */
    public function show(Dependency $dependency)
    {
        $this->authorize('admin.dependency.show', $dependency);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Dependency $dependency
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(Dependency $dependency)
    {
        $this->authorize('admin.dependency.edit', $dependency);


        return view('admin.dependency.edit', [
            'dependency' => $dependency,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateDependency $request
     * @param Dependency $dependency
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateDependency $request, Dependency $dependency)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values Dependency
        $dependency->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/dependencies'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/dependencies');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyDependency $request
     * @param Dependency $dependency
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyDependency $request, Dependency $dependency)
    {
        $dependency->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyDependency $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyDependency $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    Dependency::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
