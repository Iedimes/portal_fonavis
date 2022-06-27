<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Typology\BulkDestroyTypology;
use App\Http\Requests\Admin\Typology\DestroyTypology;
use App\Http\Requests\Admin\Typology\IndexTypology;
use App\Http\Requests\Admin\Typology\StoreTypology;
use App\Http\Requests\Admin\Typology\UpdateTypology;
use App\Models\Typology;
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

class TypologiesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexTypology $request
     * @return array|Factory|View
     */
    public function index(IndexTypology $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(Typology::class)->processRequestAndGet(
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

        return view('admin.typology.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.typology.create');

        return view('admin.typology.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreTypology $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreTypology $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the Typology
        $typology = Typology::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/typologies'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/typologies');
    }

    /**
     * Display the specified resource.
     *
     * @param Typology $typology
     * @throws AuthorizationException
     * @return void
     */
    public function show(Typology $typology)
    {
        $this->authorize('admin.typology.show', $typology);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Typology $typology
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(Typology $typology)
    {
        $this->authorize('admin.typology.edit', $typology);


        return view('admin.typology.edit', [
            'typology' => $typology,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateTypology $request
     * @param Typology $typology
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateTypology $request, Typology $typology)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values Typology
        $typology->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/typologies'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/typologies');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyTypology $request
     * @param Typology $typology
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyTypology $request, Typology $typology)
    {
        $typology->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyTypology $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyTypology $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    Typology::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
