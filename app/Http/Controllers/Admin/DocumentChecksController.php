<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DocumentCheck\BulkDestroyDocumentCheck;
use App\Http\Requests\Admin\DocumentCheck\DestroyDocumentCheck;
use App\Http\Requests\Admin\DocumentCheck\IndexDocumentCheck;
use App\Http\Requests\Admin\DocumentCheck\StoreDocumentCheck;
use App\Http\Requests\Admin\DocumentCheck\UpdateDocumentCheck;
use App\Models\DocumentCheck;
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

class DocumentChecksController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexDocumentCheck $request
     * @return array|Factory|View
     */
    public function index(IndexDocumentCheck $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(DocumentCheck::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'project_id', 'document_id'],

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

        return view('admin.document-check.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.document-check.create');

        return view('admin.document-check.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreDocumentCheck $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreDocumentCheck $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the DocumentCheck
        $documentCheck = DocumentCheck::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/document-checks'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/document-checks');
    }

    /**
     * Display the specified resource.
     *
     * @param DocumentCheck $documentCheck
     * @throws AuthorizationException
     * @return void
     */
    public function show(DocumentCheck $documentCheck)
    {
        $this->authorize('admin.document-check.show', $documentCheck);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param DocumentCheck $documentCheck
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(DocumentCheck $documentCheck)
    {
        $this->authorize('admin.document-check.edit', $documentCheck);


        return view('admin.document-check.edit', [
            'documentCheck' => $documentCheck,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateDocumentCheck $request
     * @param DocumentCheck $documentCheck
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateDocumentCheck $request, DocumentCheck $documentCheck)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values DocumentCheck
        $documentCheck->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/document-checks'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/document-checks');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyDocumentCheck $request
     * @param DocumentCheck $documentCheck
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyDocumentCheck $request, DocumentCheck $documentCheck)
    {
        $documentCheck->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyDocumentCheck $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyDocumentCheck $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DocumentCheck::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
