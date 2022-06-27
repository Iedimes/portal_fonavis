<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Stage\BulkDestroyStage;
use App\Http\Requests\Admin\Stage\DestroyStage;
use App\Http\Requests\Admin\Stage\IndexStage;
use App\Http\Requests\Admin\Stage\StoreStage;
use App\Http\Requests\Admin\Stage\UpdateStage;
use App\Models\Stage;
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

class StagesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexStage $request
     * @return array|Factory|View
     */
    public function index(IndexStage $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(Stage::class)->processRequestAndGet(
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

        return view('admin.stage.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.stage.create');

        return view('admin.stage.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreStage $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreStage $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the Stage
        $stage = Stage::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/stages'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/stages');
    }

    /**
     * Display the specified resource.
     *
     * @param Stage $stage
     * @throws AuthorizationException
     * @return void
     */
    public function show(Stage $stage)
    {
        $this->authorize('admin.stage.show', $stage);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Stage $stage
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(Stage $stage)
    {
        $this->authorize('admin.stage.edit', $stage);


        return view('admin.stage.edit', [
            'stage' => $stage,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateStage $request
     * @param Stage $stage
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateStage $request, Stage $stage)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values Stage
        $stage->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/stages'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/stages');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyStage $request
     * @param Stage $stage
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyStage $request, Stage $stage)
    {
        $stage->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyStage $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyStage $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    Stage::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
