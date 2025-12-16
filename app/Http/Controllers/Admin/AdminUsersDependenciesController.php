<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminUsersDependency\BulkDestroyAdminUsersDependency;
use App\Http\Requests\Admin\AdminUsersDependency\DestroyAdminUsersDependency;
use App\Http\Requests\Admin\AdminUsersDependency\IndexAdminUsersDependency;
use App\Http\Requests\Admin\AdminUsersDependency\StoreAdminUsersDependency;
use App\Http\Requests\Admin\AdminUsersDependency\UpdateAdminUsersDependency;
use App\Models\AdminUsersDependency;
use App\Models\AdminUser;
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

class AdminUsersDependenciesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexAdminUsersDependency $request
     * @return array|Factory|View
     */
    public function index(IndexAdminUsersDependency $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(AdminUsersDependency::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'admin_user_id', 'dependency_id'],

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

        return view('admin.admin-users-dependency.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.admin-users-dependency.create');

        $admin_user = AdminUser::select('id', 'email', 'first_name', 'last_name')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
        $dependency = Dependency::select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('admin.admin-users-dependency.create', compact('admin_user', 'dependency'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreAdminUsersDependency $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreAdminUsersDependency $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the AdminUsersDependency
        $adminUsersDependency = AdminUsersDependency::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/admin-users-dependencies'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/admin-users-dependencies');
    }

    /**
     * Display the specified resource.
     *
     * @param AdminUsersDependency $adminUsersDependency
     * @throws AuthorizationException
     * @return void
     */
    public function show(AdminUsersDependency $adminUsersDependency)
    {
        $this->authorize('admin.admin-users-dependency.show', $adminUsersDependency);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param AdminUsersDependency $adminUsersDependency
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(AdminUsersDependency $adminUsersDependency)
    {
        $this->authorize('admin.admin-users-dependency.edit', $adminUsersDependency);

        $admin_user = AdminUser::select('id', 'email', 'first_name', 'last_name')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
        $dependency = Dependency::select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('admin.admin-users-dependency.edit', compact('adminUsersDependency', 'admin_user', 'dependency'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateAdminUsersDependency $request
     * @param AdminUsersDependency $adminUsersDependency
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateAdminUsersDependency $request, AdminUsersDependency $adminUsersDependency)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values AdminUsersDependency
        $adminUsersDependency->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/admin-users-dependencies'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/admin-users-dependencies');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyAdminUsersDependency $request
     * @param AdminUsersDependency $adminUsersDependency
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyAdminUsersDependency $request, AdminUsersDependency $adminUsersDependency)
    {
        $adminUsersDependency->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyAdminUsersDependency $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyAdminUsersDependency $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    AdminUsersDependency::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
