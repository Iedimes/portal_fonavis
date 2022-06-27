<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ModalityHasLand\BulkDestroyModalityHasLand;
use App\Http\Requests\Admin\ModalityHasLand\DestroyModalityHasLand;
use App\Http\Requests\Admin\ModalityHasLand\IndexModalityHasLand;
use App\Http\Requests\Admin\ModalityHasLand\StoreModalityHasLand;
use App\Http\Requests\Admin\ModalityHasLand\UpdateModalityHasLand;
use App\Models\ModalityHasLand;
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

class ModalityHasLandsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexModalityHasLand $request
     * @return array|Factory|View
     */
    public function index(IndexModalityHasLand $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(ModalityHasLand::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'modality_id', 'land_id'],

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

        return view('admin.modality-has-land.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.modality-has-land.create');

        return view('admin.modality-has-land.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreModalityHasLand $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreModalityHasLand $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the ModalityHasLand
        $modalityHasLand = ModalityHasLand::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/modality-has-lands'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/modality-has-lands');
    }

    /**
     * Display the specified resource.
     *
     * @param ModalityHasLand $modalityHasLand
     * @throws AuthorizationException
     * @return void
     */
    public function show(ModalityHasLand $modalityHasLand)
    {
        $this->authorize('admin.modality-has-land.show', $modalityHasLand);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param ModalityHasLand $modalityHasLand
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(ModalityHasLand $modalityHasLand)
    {
        $this->authorize('admin.modality-has-land.edit', $modalityHasLand);


        return view('admin.modality-has-land.edit', [
            'modalityHasLand' => $modalityHasLand,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateModalityHasLand $request
     * @param ModalityHasLand $modalityHasLand
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateModalityHasLand $request, ModalityHasLand $modalityHasLand)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values ModalityHasLand
        $modalityHasLand->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/modality-has-lands'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/modality-has-lands');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyModalityHasLand $request
     * @param ModalityHasLand $modalityHasLand
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyModalityHasLand $request, ModalityHasLand $modalityHasLand)
    {
        $modalityHasLand->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyModalityHasLand $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyModalityHasLand $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    ModalityHasLand::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
