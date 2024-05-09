<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Project\BulkDestroyProject;
use App\Http\Requests\Admin\Project\DestroyProject;
use App\Http\Requests\Admin\Project\IndexProject;
use App\Http\Requests\Admin\Project\StoreProject;
use App\Http\Requests\Admin\Project\UpdateProject;
use App\Http\Requests\Admin\ProjectStatus\StoreProjectStatusE;
use App\Models\Project;
use App\Models\Land_project;
use App\Models\Assignment;
use App\Models\Stage;
use App\Models\ProjectStatus;
use App\Models\Sat;
use App\Models\Departamento;
use App\Models\ProjectHasPostulantes;
use App\Models\Documents;
use App\Models\Documentsmissing;
use App\Models\Medium;
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
use Illuminate\Support\Facades\Auth;

class ProjectsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexProject $request
     * @return array|Factory|View
     */
    public function index(IndexProject $request)
    {
        // create and AdminListing instance for a specific model and

        $usuarioRol = Auth::user()->rol_app->dependency_id;

        $data = AdminListing::create(Project::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'name', 'phone', 'sat_id', 'state_id', 'city_id', 'modalidad_id', 'leader_name', 'localidad'],

            // set columns to searchIn
            ['id', 'name','sat_id', 'city_id', 'modalidad_id', 'leader_name', 'localidad']

        );

        //return $data;

        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.project.index', ['data' => $data, 'usuarioRol' => $usuarioRol]);
    }






    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.project.create');

        return view('admin.project.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreProject $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreProject $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the Project
        $project = Project::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/projects'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/projects');
    }

    /**
     * Display the specified resource.
     *
     * @param Project $project
     * @throws AuthorizationException
     * @return void
     */
    public function show(Project $project)
    {
        $this->authorize('admin.project.show', $project);
        $id=$project->id;
        $project_type= Land_project::where('land_id',$project->land_id)->first();
        $postulantes = ProjectHasPostulantes::where('project_id', $id)->get();
        $docproyecto = Assignment::where('project_type_id',$project_type->project_type_id)
        ->where('category_id',1)
        ->get();
        $history = ProjectStatus::where('project_id',$project['id'])
                    ->orderBy('created_at')
                    ->get();

                    // Verificar si se ha cargado un archivo para cada elemento
        $uploadedFiles = [];
        foreach ($docproyecto as $item) {
            $uploadedFile = Documents::where('project_id', $project->id)
                ->where('document_id', $item->document_id)
                ->first();
            //return $uploadedFile;
            $documentExists = /*$uploadedFile &&*/ $uploadedFile  ? $uploadedFile->file_path : false;
            //return $documentExists;
            $uploadedFiles[$item->document_id] = $documentExists;
        }

        //return $history;

        return view('admin.project.show', compact('project', 'docproyecto','history', 'postulantes','uploadedFiles'));
    }

    public function showDGJN(Project $project)
    {
        $this->authorize('admin.project.show', $project);
        $id=$project->id;
        $project_type= Land_project::where('land_id',$project->land_id)->first();
        $postulantes = ProjectHasPostulantes::where('project_id', $id)->get();
        $docproyecto = Assignment::where('project_type_id',$project_type->project_type_id)
        ->where('category_id',1)
        ->get();
        $history = ProjectStatus::where('project_id',$project['id'])
                    ->orderBy('created_at')
                    ->get();

                    // Verificar si se ha cargado un archivo para cada elemento
        $uploadedFiles = [];
        foreach ($docproyecto as $item) {
            $uploadedFile = Documents::where('project_id', $project->id)
                ->where('document_id', $item->document_id)
                ->first();
            //return $uploadedFile;
            $documentExists = /*$uploadedFile &&*/ $uploadedFile  ? $uploadedFile->file_path : false;
            //return $documentExists;
            $uploadedFiles[$item->document_id] = $documentExists;
        }

        //return $history;

        return view('admin.project.DGJN.show', compact('project', 'docproyecto','history', 'postulantes','uploadedFiles'));
    }

    public function showDGJNFALTANTE(Project $project)
    {
        // $this->authorize('admin.project.show', $project);
        //return "DOCUMENTO FALTANTE";
        $id=$project->id;
        $project_type= Land_project::where('land_id',$project->land_id)->first();
        $postulantes = ProjectHasPostulantes::where('project_id', $id)->get();
        $documentos = Documentsmissing::where('project_id',$id)->get();
        $history = ProjectStatus::where('project_id',$project['id'])
                    ->orderBy('created_at')
                    ->get();

                    // Verificar si se ha cargado un archivo para cada elemento
        $uploadedFiles = [];
        foreach ($documentos as $item) {
            $uploadedFile = Documentsmissing::where('project_id', $project->id)
                ->where('document_id', $item->document_id)
                ->first();
            //return $uploadedFile;
            $documentExists = /*$uploadedFile &&*/ $uploadedFile  ? $uploadedFile->file_path : false;
            //return $documentExists;
            $uploadedFiles[$item->document_id] = $documentExists;
        }

        //return $history;

        return view('admin.project.DGJN.showFaltante', compact('project','history', 'postulantes','uploadedFiles', 'documentos'));
    }

    public function showFONAVIS(Project $project)
{
    // $this->authorize('admin.project.show', $project);
    $id = $project->id;
    $stageId = $project->getestado->stage_id;

    if ($stageId == 3 || $stageId == 13 || $stageId == 18) {
        $proyectoEstado = ProjectStatus::where('project_id', $id)
            ->where('stage_id', $stageId)
            ->get();
    } else {
        $proyectoEstado = collect();
    }

    $project_type = Land_project::where('land_id', $project->land_id)->first();
    $postulantes = ProjectHasPostulantes::where('project_id', $id)->get();

    return view('admin.project.FONAVIS.show', compact('project', 'postulantes', 'proyectoEstado'));
}

    public function showFONAVISSOCIAL(Project $project)
    {
        // $this->authorize('admin.project.show', $project);
        $id=$project->id;
        $proyectoEstado = ProjectStatus::where('project_id', $id)->where('stage_id', 9)->get();
        $project_type= Land_project::where('land_id',$project->land_id)->first();
        $postulantes = ProjectHasPostulantes::where('project_id', $id)->get();





        //return $history;

        return view('admin.project.FONAVIS.showSocial', compact('project', 'postulantes', 'proyectoEstado'));
    }

    public function showDGSO(Project $project)
    {
        //$this->authorize('admin.project.show', $project);
        $id=$project->id;
        $project_type= Land_project::where('land_id',$project->land_id)->first();
        $postulantes = ProjectHasPostulantes::where('project_id', $id)->get();
        $docproyecto = Assignment::where('project_type_id',$project_type->project_type_id)
        ->where('category_id',1)
        ->get();
        $history = ProjectStatus::where('project_id',$project['id'])
                    ->orderBy('created_at')
                    ->get();

                    // Verificar si se ha cargado un archivo para cada elemento
        $uploadedFiles = [];
        foreach ($docproyecto as $item) {
            $uploadedFile = Documents::where('project_id', $project->id)
                ->where('document_id', $item->document_id)
                ->first();
            //return $uploadedFile;
            $documentExists = /*$uploadedFile &&*/ $uploadedFile  ? $uploadedFile->file_path : false;
            //return $documentExists;
            $uploadedFiles[$item->document_id] = $documentExists;
        }

        //return $history;

        return view('admin.project.DGSO.show', compact('project', 'docproyecto','history', 'postulantes','uploadedFiles'));
    }

    public function showFONAVISTECNICO(Project $project)
    {
       //$this->authorize('admin.project.show', $project);
       $id=$project->id;
       $project_type= Land_project::where('land_id',$project->land_id)->first();
       $postulantes = ProjectHasPostulantes::where('project_id', $id)->get();
       $docproyecto = Assignment::where('project_type_id',$project_type->project_type_id)
       ->whereIn('category_id',[2])
       ->get();
       $history = ProjectStatus::where('project_id',$project['id'])
                   ->orderBy('created_at')
                   ->get();

                   // Verificar si se ha cargado un archivo para cada elemento
       $uploadedFiles = [];
       foreach ($docproyecto as $item) {
           $uploadedFile = Documents::where('project_id', $project->id)
               ->where('document_id', $item->document_id)
               ->first();
           //return $uploadedFile;
           $documentExists = /*$uploadedFile &&*/ $uploadedFile  ? $uploadedFile->file_path : false;
           //return $documentExists;
           $uploadedFiles[$item->document_id] = $documentExists;
       }

       //return $history;

       return view('admin.project.FONAVIS.showFonavisTecnico', compact('project', 'docproyecto','history', 'postulantes','uploadedFiles'));
    }

    public function showFONAVISTECNICODOS(Project $project)
    {
       //$this->authorize('admin.project.show', $project);
       $id=$project->id;
       $project_type= Land_project::where('land_id',$project->land_id)->first();
       $postulantes = ProjectHasPostulantes::where('project_id', $id)->get();
       $docproyecto = Assignment::where('project_type_id',$project_type->project_type_id)
       ->whereIn('category_id',[3])
       ->get();
       $history = ProjectStatus::where('project_id',$project['id'])
                   ->orderBy('created_at')
                   ->get();

                   // Verificar si se ha cargado un archivo para cada elemento
       $uploadedFiles = [];
       foreach ($docproyecto as $item) {
           $uploadedFile = Documents::where('project_id', $project->id)
               ->where('document_id', $item->document_id)
               ->first();
           //return $uploadedFile;
           $documentExists = /*$uploadedFile &&*/ $uploadedFile  ? $uploadedFile->file_path : false;
           //return $documentExists;
           $uploadedFiles[$item->document_id] = $documentExists;
       }

       //return $history;

       return view('admin.project.FONAVIS.showFonavisTecnicoDos', compact('project', 'docproyecto','history', 'postulantes','uploadedFiles'));
    }

    public function showDIGH(Project $project)
    {
       //$this->authorize('admin.project.show', $project);
       $id=$project->id;
       $project_type= Land_project::where('land_id',$project->land_id)->first();
       $postulantes = ProjectHasPostulantes::where('project_id', $id)->get();
       $docproyecto = Assignment::where('project_type_id',$project_type->project_type_id)
       ->where('category_id',2)
       ->get();
       $history = ProjectStatus::where('project_id',$project['id'])
                   ->orderBy('created_at')
                   ->get();

                   // Verificar si se ha cargado un archivo para cada elemento
       $uploadedFiles = [];
       foreach ($docproyecto as $item) {
           $uploadedFile = Documents::where('project_id', $project->id)
               ->where('document_id', $item->document_id)
               ->first();
           //return $uploadedFile;
           $documentExists = /*$uploadedFile &&*/ $uploadedFile  ? $uploadedFile->file_path : false;
           //return $documentExists;
           $uploadedFiles[$item->document_id] = $documentExists;
       }

       //return $history;

       return view('admin.project.DIGH.showDIGH', compact('project', 'docproyecto','history', 'postulantes','uploadedFiles'));
    }

    public function showDSGO(Project $project)
    {
       //$this->authorize('admin.project.show', $project);
       $id=$project->id;
       $project_type= Land_project::where('land_id',$project->land_id)->first();
       $postulantes = ProjectHasPostulantes::where('project_id', $id)->get();
       $docproyecto = Assignment::where('project_type_id',$project_type->project_type_id)
       ->where('category_id',3)
       ->get();
       $history = ProjectStatus::where('project_id',$project['id'])
                   ->orderBy('created_at')
                   ->get();

                   // Verificar si se ha cargado un archivo para cada elemento
       $uploadedFiles = [];
       foreach ($docproyecto as $item) {
           $uploadedFile = Documents::where('project_id', $project->id)
               ->where('document_id', $item->document_id)
               ->first();
           //return $uploadedFile;
           $documentExists = /*$uploadedFile &&*/ $uploadedFile  ? $uploadedFile->file_path : false;
           //return $documentExists;
           $uploadedFiles[$item->document_id] = $documentExists;
       }

       //return $history;

       return view('admin.project.DSGO.show', compact('project', 'docproyecto','history', 'postulantes','uploadedFiles'));
    }

    public function showFONAVISADJ(Project $project)
    {
       //$this->authorize('admin.project.show', $project);
       $id=$project->id;
       $project_type= Land_project::where('land_id',$project->land_id)->first();
       $postulantes = ProjectHasPostulantes::where('project_id', $id)->get();
       $docproyecto = Assignment::where('project_type_id',$project_type->project_type_id)
       ->where('category_id',3)
       ->get();
       $history = ProjectStatus::where('project_id',$project['id'])
                   ->orderBy('created_at')
                   ->get();

                   // Verificar si se ha cargado un archivo para cada elemento
       $uploadedFiles = [];
       foreach ($docproyecto as $item) {
           $uploadedFile = Documents::where('project_id', $project->id)
               ->where('document_id', $item->document_id)
               ->first();
           //return $uploadedFile;
           $documentExists = /*$uploadedFile &&*/ $uploadedFile  ? $uploadedFile->file_path : false;
           //return $documentExists;
           $uploadedFiles[$item->document_id] = $documentExists;
       }

       //return $history;

       return view('admin.project.FONAVIS.showFonavisAdj', compact('project', 'docproyecto','history', 'postulantes','uploadedFiles'));
    }


    public function transition(Project $project)
    {
        $project->getEstado->getStage->id;
        $estado=$project->getEstado->getStage->id;
        $user = Auth::user()->id;
        $email = Auth::user()->email;
        $stages = Stage::where('id','!=',$project->getEstado->getStage->id)->get();

        /*if ($workflowState->id == 26) {
            $mensaje = 'Esta impresion del documento quedara registrada en el historial!!';
        }else{
            $mensaje = 'Este cambio de estado quedara registrado en el historial de la solicitud';
        }*/
        $mensaje = 'Este cambio de estado quedara registrado en el historial del Proyecto';

        return view('admin.project.transition', compact('project', 'user','mensaje','stages','email', 'estado'));

    }

    public function transitionEliminar(Project $project)
    {
        $project->getEstado->getStage->id;
        $user = Auth::user()->id;
        $email = Auth::user()->email;
        $stages = Stage::where('id','!=',$project->getEstado->getStage->id)->get();

        /*if ($workflowState->id == 26) {
            $mensaje = 'Esta impresion del documento quedara registrada en el historial!!';
        }else{
            $mensaje = 'Este cambio de estado quedara registrado en el historial de la solicitud';
        }*/
        $mensaje = 'Este cambio de estado quedara registrado en el historial del Proyecto';

        return view('admin.project.transitionEliminar', compact('project', 'user','mensaje','stages','email'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Project $project
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(Project $project)
    {
        $this->authorize('admin.project.edit', $project);


        return view('admin.project.edit', [
            'project' => $project,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateProject $request
     * @param Project $project
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateProject $request, Project $project)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values Project
        $project->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/projects'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/projects');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyProject $request
     * @param Project $project
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyProject $request, Project $project)
    {
        $project->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyProject $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyProject $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    Project::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
