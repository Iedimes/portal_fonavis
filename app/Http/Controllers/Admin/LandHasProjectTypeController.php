<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LandHasProjectType\BulkDestroyLandHasProjectType;
use App\Http\Requests\Admin\LandHasProjectType\DestroyLandHasProjectType;
use App\Http\Requests\Admin\LandHasProjectType\IndexLandHasProjectType;
use App\Http\Requests\Admin\LandHasProjectType\StoreLandHasProjectType;
use App\Http\Requests\Admin\LandHasProjectType\UpdateLandHasProjectType;
use App\Models\LandHasProjectType;
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

class LandHasProjectTypeController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexLandHasProjectType $request
     * @return array|Factory|View
     */
    public function index(IndexLandHasProjectType $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(LandHasProjectType::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'land_id', 'project_type_id'],

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

        return view('admin.land-has-project-type.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.land-has-project-type.create');

        return view('admin.land-has-project-type.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreLandHasProjectType $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreLandHasProjectType $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the LandHasProjectType
        $landHasProjectType = LandHasProjectType::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/land-has-project-types'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/land-has-project-types');
    }

    /**
     * Display the specified resource.
     *
     * @param LandHasProjectType $landHasProjectType
     * @throws AuthorizationException
     * @return void
     */
    public function show(LandHasProjectType $landHasProjectType)
    {
        $this->authorize('admin.land-has-project-type.show', $landHasProjectType);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param LandHasProjectType $landHasProjectType
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(LandHasProjectType $landHasProjectType)
    {
        $this->authorize('admin.land-has-project-type.edit', $landHasProjectType);


        return view('admin.land-has-project-type.edit', [
            'landHasProjectType' => $landHasProjectType,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateLandHasProjectType $request
     * @param LandHasProjectType $landHasProjectType
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateLandHasProjectType $request, LandHasProjectType $landHasProjectType)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values LandHasProjectType
        $landHasProjectType->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/land-has-project-types'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/land-has-project-types');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyLandHasProjectType $request
     * @param LandHasProjectType $landHasProjectType
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyLandHasProjectType $request, LandHasProjectType $landHasProjectType)
    {
        $landHasProjectType->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyLandHasProjectType $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyLandHasProjectType $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    LandHasProjectType::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
