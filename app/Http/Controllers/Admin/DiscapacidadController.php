<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Discapacidad\BulkDestroyDiscapacidad;
use App\Http\Requests\Admin\Discapacidad\DestroyDiscapacidad;
use App\Http\Requests\Admin\Discapacidad\IndexDiscapacidad;
use App\Http\Requests\Admin\Discapacidad\StoreDiscapacidad;
use App\Http\Requests\Admin\Discapacidad\UpdateDiscapacidad;
use App\Models\Discapacidad;
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

class DiscapacidadController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexDiscapacidad $request
     * @return array|Factory|View
     */
    public function index(IndexDiscapacidad $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(Discapacidad::class)->processRequestAndGet(
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

        return view('admin.discapacidad.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.discapacidad.create');

        return view('admin.discapacidad.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreDiscapacidad $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreDiscapacidad $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the Discapacidad
        $discapacidad = Discapacidad::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/discapacidads'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/discapacidads');
    }

    /**
     * Display the specified resource.
     *
     * @param Discapacidad $discapacidad
     * @throws AuthorizationException
     * @return void
     */
    public function show(Discapacidad $discapacidad)
    {
        $this->authorize('admin.discapacidad.show', $discapacidad);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Discapacidad $discapacidad
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(Discapacidad $discapacidad)
    {
        $this->authorize('admin.discapacidad.edit', $discapacidad);


        return view('admin.discapacidad.edit', [
            'discapacidad' => $discapacidad,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateDiscapacidad $request
     * @param Discapacidad $discapacidad
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateDiscapacidad $request, Discapacidad $discapacidad)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values Discapacidad
        $discapacidad->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/discapacidads'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/discapacidads');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyDiscapacidad $request
     * @param Discapacidad $discapacidad
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyDiscapacidad $request, Discapacidad $discapacidad)
    {
        $discapacidad->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyDiscapacidad $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyDiscapacidad $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    Discapacidad::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
