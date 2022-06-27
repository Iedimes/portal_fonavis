<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Land\BulkDestroyLand;
use App\Http\Requests\Admin\Land\DestroyLand;
use App\Http\Requests\Admin\Land\IndexLand;
use App\Http\Requests\Admin\Land\StoreLand;
use App\Http\Requests\Admin\Land\UpdateLand;
use App\Models\Land;
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

class LandsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexLand $request
     * @return array|Factory|View
     */
    public function index(IndexLand $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(Land::class)->processRequestAndGet(
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

        return view('admin.land.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.land.create');

        return view('admin.land.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreLand $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreLand $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the Land
        $land = Land::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/lands'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/lands');
    }

    /**
     * Display the specified resource.
     *
     * @param Land $land
     * @throws AuthorizationException
     * @return void
     */
    public function show(Land $land)
    {
        $this->authorize('admin.land.show', $land);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Land $land
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(Land $land)
    {
        $this->authorize('admin.land.edit', $land);


        return view('admin.land.edit', [
            'land' => $land,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateLand $request
     * @param Land $land
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateLand $request, Land $land)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values Land
        $land->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/lands'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/lands');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyLand $request
     * @param Land $land
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyLand $request, Land $land)
    {
        $land->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyLand $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyLand $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    Land::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
