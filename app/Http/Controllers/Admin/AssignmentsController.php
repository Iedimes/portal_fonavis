<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Assignment\BulkDestroyAssignment;
use App\Http\Requests\Admin\Assignment\DestroyAssignment;
use App\Http\Requests\Admin\Assignment\IndexAssignment;
use App\Http\Requests\Admin\Assignment\StoreAssignment;
use App\Http\Requests\Admin\Assignment\UpdateAssignment;
use App\Models\Assignment;
use App\Models\Document;
use App\Models\Category;
use App\Models\ProjectType;
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

class AssignmentsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexAssignment $request
     * @return array|Factory|View
     */
    public function index(IndexAssignment $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(Assignment::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'document_id', 'category_id', 'project_type_id', 'stage_id'],

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

        return view('admin.assignment.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.assignment.create');

        $document = Document::all();
        $category = Category::all();
        $pt=ProjectType::all();
        $stage=Stage::all();




        return view('admin.assignment.create', compact('document','category','pt','stage'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreAssignment $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreAssignment $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        $sanitized ['document_id']=  $request->getDocumentId();
        $sanitized ['category_id']=  $request->getCategoryId();
        $sanitized ['project_type_id']=  $request->getPtId();
        $sanitized ['stage_id']=  $request->getStageId();


       // return $request->getPtId();

        // Store the Assignment
        $assignment = Assignment::create($sanitized);




        if ($request->ajax()) {
            return ['redirect' => url('admin/assignments'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/assignments');
    }

    /**
     * Display the specified resource.
     *
     * @param Assignment $assignment
     * @throws AuthorizationException
     * @return void
     */
    public function show(Assignment $assignment)
    {
        $this->authorize('admin.assignment.show', $assignment);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Assignment $assignment
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(Assignment $assignment)
    {
        $this->authorize('admin.assignment.edit', $assignment);
        $document = Document::all();
        $category = Category::all();
        $pt=ProjectType::all();
        $stage=Stage::all();



        return view('admin.assignment.edit', [
            'assignment' => $assignment,
            'document' => $document,
            'category'=> $category,
            'pt' => $pt,
            'stage'=>$stage,


        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateAssignment $request
     * @param Assignment $assignment
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateAssignment $request, Assignment $assignment)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        $sanitized ['document_id']=  $request->getDocumentId();
        $sanitized ['category_id']=  $request->getCategoryId();
        $sanitized ['project_type_id']=  $request->getPtId();
        $sanitized ['stage_id']=  $request->getStageId();



        // Update changed values Assignment
        $assignment->update($sanitized);

        //$sanitized ['document_id']=  $request->getDocumentId();


        if ($request->ajax()) {
            return [
                'redirect' => url('admin/assignments'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/assignments');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyAssignment $request
     * @param Assignment $assignment
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyAssignment $request, Assignment $assignment)
    {
        $assignment->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyAssignment $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyAssignment $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    Assignment::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
