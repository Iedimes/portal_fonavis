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
use App\Models\ProjectStatusF;
use App\Models\Sat;
use App\Models\AdminUser;
use App\Models\Dependency;
use App\Models\Departamento;
use App\Models\ProjectHasPostulantes;
use App\Models\Documents;
use App\Models\Documentsmissing;
use App\Models\Medium;
use App\Models\Modality;
use App\Models\Land;
use App\Models\Typology;
use App\Models\Distrito;
use App\Models\User;
use App\Models\DighObservation;
use App\Models\SIG005;
use App\Models\SIG006;
use App\Models\SHMCER;
use App\Models\PRMCLI;
use App\Models\IVMSOL;
use App\Models\Postulante;
use Carbon\Carbon;
use App\Models\Discapacidad;
use App\Models\Parentesco;
use App\Models\PostulanteHasBeneficiary;
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
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

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
        // Obtener el rol del usuario
        $usuario=Auth::user()->id;
        $usuarioRol = Auth::user()->rol_app->dependency_id;
        $dependencia = Dependency::where('id', $usuarioRol)->first();

        // Crear una instancia de AdminListing
        $listing = AdminListing::create(Project::class);

        // Procesar sin ningún filtro personalizado
        $data = $listing->processRequestAndGet(
            $request,
            ['id', 'name', 'phone', 'sat_id', 'state_id', 'city_id', 'modalidad_id', 'leader_name', 'localidad'],
            ['id', 'name', 'sat_id', 'city_id', 'modalidad_id', 'leader_name', 'localidad']
        );

        // Comprobamos si es una solicitud AJAX
        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }

            return ['data' => $data];
        }

        // Retornar la vista con los datos (sin filtro en el backend)
        return view('admin.project.index', ['data' => $data, 'usuarioRol' => $usuarioRol, 'dependencia' => $dependencia]);
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

        $sat = Sat::where('NucRuc', '!=', null)
            ->where('NucEst', '=', 'H')
            ->select('NucNomSat', 'NucCod', 'NucCont')
            ->get();

        $modalidad = Modality::all();

        $dep = [18, 19, 20, 21, 999];

        // Solo departamentos, sin localidades
        $departamentos = Departamento::whereNotIn('DptoId', $dep)
                        ->orderBy('DptoNom', 'asc')
                        ->get();

        return view('admin.project.create', compact('sat', 'modalidad', 'departamentos'));
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

        // Reemplazar sat_id con el código real
        $sanitized['sat_id'] = $request->getSatId();
        $sanitized['modalidad_id'] = $request->getModalidadId();
        $sanitized['land_id'] = $request->getLandId();
        $sanitized['typology_id'] = $request->getTypologyId();

        // Agregar state_id y city_id igual que los anteriores
        $sanitized['state_id'] = $request->getStateId();
        $sanitized['city_id'] = $request->getCityId();

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
    $id = $project->id;
    $project_type = Land_project::where('land_id', $project->land_id)->first();
    $postulantes = ProjectHasPostulantes::where('project_id', $id)->get();
    $docproyecto = Assignment::where('project_type_id', $project_type->project_type_id)
        ->where('category_id', 1)
        ->get();

    $docproyectoNoExcluyentes = Assignment::where('project_type_id', $project_type->project_type_id)
        ->where('category_id', 4)
        ->get();

    $docproyectoCondominio = Assignment::where('project_type_id', $project_type->project_type_id)
       ->where('category_id', 5)
       ->get();

    $docproyectoIndi = Assignment::where('project_type_id', $project_type->project_type_id)
       ->where('category_id', 6)
       ->get();

    $history = ProjectStatus::where('project_id', $project['id'])
        ->orderBy('created_at')
        ->get();

    // Verificar si se ha cargado un archivo para cada elemento
    $uploadedFiles = [];
    foreach ($docproyecto as $item) {
        $uploadedFile = Documents::where('project_id', $project->id)
            ->where('document_id', $item->document_id)
            ->first();
        $documentExists = $uploadedFile ? $uploadedFile->file_path : false;
        $uploadedFiles[$item->document_id] = $documentExists;
    }

    // Verificar si se ha cargado un archivo para cada elemento
    $uploadedFiles1 = [];
    foreach ($docproyectoNoExcluyentes as $item) {
        $uploadedFile1 = Documents::where('project_id', $project->id)
            ->where('document_id', $item->document_id)
            ->first();
        $documentExists = $uploadedFile1 ? $uploadedFile1->file_path : false;
        $uploadedFiles1[$item->document_id] = $documentExists;
    }

    // Verificar si se ha cargado un archivo para cada elemento
    $uploadedFiles2 = [];
    foreach ($docproyectoCondominio as $item) {
        $uploadedFile2 = Documents::where('project_id', $project->id)
            ->where('document_id', $item->document_id)
            ->first();
        $documentExists = $uploadedFile2 ? $uploadedFile2->file_path : false;
        $uploadedFiles2[$item->document_id] = $documentExists;
    }

    // Verificar si se ha cargado un archivo para cada elemento
    $uploadedFiles3 = [];
    foreach ($docproyectoIndi as $item) {
        $uploadedFile3 = Documents::where('project_id', $project->id)
            ->where('document_id', $item->document_id)
            ->first();
        $documentExists = $uploadedFile3 ? $uploadedFile3->file_path : false;
        $uploadedFiles3[$item->document_id] = $documentExists;
    }

    // Obtener los documentos no excluyentes faltantes
    $missingDocuments = [];
    foreach ($docproyectoNoExcluyentes as $item) {
        if (!isset($uploadedFiles1[$item->document_id]) || !$uploadedFiles1[$item->document_id]) {
            $missingDocuments[] = $item->document->name;
        }
    }

    return view('admin.project.show', compact('project', 'docproyecto', 'history', 'postulantes', 'uploadedFiles', 'docproyectoNoExcluyentes', 'docproyectoCondominio', 'docproyectoIndi', 'uploadedFiles1', 'uploadedFiles2', 'uploadedFiles3', 'missingDocuments'));
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
        $docproyectoNoExcluyentes = Assignment::where('project_type_id', $project_type->project_type_id)
        ->where('category_id', 4)
        ->get();

        $docproyectoCondominio = Assignment::where('project_type_id', $project_type->project_type_id)
        ->where('category_id', 5)
        ->get();

        $history = ProjectStatus::where('project_id',$project['id'])
                    ->orderBy('created_at')
                    ->get();
                    $uploadedFiles = [];
                    foreach ($docproyecto as $item) {
                        $uploadedFile = Documents::where('project_id', $project->id)
                            ->where('document_id', $item->document_id)
                            ->first();
                        $documentExists = $uploadedFile ? $uploadedFile->file_path : false;
                        $uploadedFiles[$item->document_id] = $documentExists;
                    }

                    // Verificar si se ha cargado un archivo para cada elemento
                    $uploadedFiles1 = [];
                    foreach ($docproyectoNoExcluyentes as $item) {
                        $uploadedFile1 = Documents::where('project_id', $project->id)
                            ->where('document_id', $item->document_id)
                            ->first();
                        $documentExists = $uploadedFile1 ? $uploadedFile1->file_path : false;
                        $uploadedFiles1[$item->document_id] = $documentExists;
                    }

                    // Verificar si se ha cargado un archivo para cada elemento
                    $uploadedFiles2 = [];
                    foreach ($docproyectoCondominio as $item) {
                        $uploadedFile2 = Documents::where('project_id', $project->id)
                            ->where('document_id', $item->document_id)
                            ->first();
                        $documentExists = $uploadedFile2 ? $uploadedFile2->file_path : false;
                        $uploadedFiles2[$item->document_id] = $documentExists;
                    }
        // Obtener los documentos no excluyentes faltantes
    $missingDocuments = [];
    foreach ($docproyectoNoExcluyentes as $item) {
        if (!isset($uploadedFiles1[$item->document_id]) || !$uploadedFiles1[$item->document_id]) {
            $missingDocuments[] = $item->document->name;
        }
    }

        //return $history;

        return view('admin.project.DGJN.show', compact('project', 'docproyecto', 'history', 'postulantes', 'uploadedFiles', 'docproyectoNoExcluyentes', 'docproyectoCondominio', 'uploadedFiles1', 'uploadedFiles2', 'missingDocuments'));
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

        // Modificar esta línea para incluir los nuevos estados 4 y 6
        if ($stageId == 3 || $stageId == 4 || $stageId == 6 || $stageId == 13 || $stageId == 18) {
           // Aquí se obtiene el estado del proyecto con la relación a la imagen
           $proyectoEstado = ProjectStatus::with('imagen')->where('project_id', $id)
               ->where('stage_id', $stageId)
               ->get();
        } else {
            $proyectoEstado = collect();
        }

        $project_type = Land_project::where('land_id', $project->land_id)->first();
        $postulantes = ProjectHasPostulantes::where('project_id', $id)->get();

        return view('admin.project.FONAVIS.show', compact('project', 'postulantes', 'proyectoEstado'));
    }

    public function showVERDOCFONAVIS(Project $project)
    {
        $this->authorize('admin.project.show', $project);
        $id=$project->id;
        $project_type= Land_project::where('land_id',$project->land_id)->first();
        $postulantes = ProjectHasPostulantes::where('project_id', $id)->get();
        $docproyecto = Assignment::where('project_type_id',$project_type->project_type_id)
        ->where('category_id',1)
        ->get();
        $docproyectoNoExcluyentes = Assignment::where('project_type_id', $project_type->project_type_id)
        ->where('category_id', 4)
        ->get();

        $docproyectoCondominio = Assignment::where('project_type_id', $project_type->project_type_id)
        ->where('category_id', 5)
        ->get();

        $history = ProjectStatus::where('project_id',$project['id'])
                    ->orderBy('created_at')
                    ->get();
                    $uploadedFiles = [];
                    foreach ($docproyecto as $item) {
                        $uploadedFile = Documents::where('project_id', $project->id)
                            ->where('document_id', $item->document_id)
                            ->first();
                        $documentExists = $uploadedFile ? $uploadedFile->file_path : false;
                        $uploadedFiles[$item->document_id] = $documentExists;
                    }

                    // Verificar si se ha cargado un archivo para cada elemento
                    $uploadedFiles1 = [];
                    foreach ($docproyectoNoExcluyentes as $item) {
                        $uploadedFile1 = Documents::where('project_id', $project->id)
                            ->where('document_id', $item->document_id)
                            ->first();
                        $documentExists = $uploadedFile1 ? $uploadedFile1->file_path : false;
                        $uploadedFiles1[$item->document_id] = $documentExists;
                    }

                    // Verificar si se ha cargado un archivo para cada elemento
                    $uploadedFiles2 = [];
                    foreach ($docproyectoCondominio as $item) {
                        $uploadedFile2 = Documents::where('project_id', $project->id)
                            ->where('document_id', $item->document_id)
                            ->first();
                        $documentExists = $uploadedFile2 ? $uploadedFile2->file_path : false;
                        $uploadedFiles2[$item->document_id] = $documentExists;
                    }
        // Obtener los documentos no excluyentes faltantes
    $missingDocuments = [];
    foreach ($docproyectoNoExcluyentes as $item) {
        if (!isset($uploadedFiles1[$item->document_id]) || !$uploadedFiles1[$item->document_id]) {
            $missingDocuments[] = $item->document->name;
        }
    }

        //return $history;

        return view('admin.project.FONAVIS.showVerDocFonavis', compact('project', 'docproyecto', 'history', 'postulantes', 'uploadedFiles', 'docproyectoNoExcluyentes', 'docproyectoCondominio', 'uploadedFiles1', 'uploadedFiles2', 'missingDocuments'));
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

       $observations = DighObservation::where('project_id', $project->id)
                                        ->pluck('observation', 'document_id'); // [document_id => 'observación']

       //return $history;

       return view('admin.project.DIGH.showDIGH', compact('project', 'docproyecto','history', 'postulantes','uploadedFiles', 'observations'));
    }

    public function saveDIGHObservation(Request $request, Project $project)
    {
        $documentId = $request->input('document_id');
        $observationText = $request->input("observation.$documentId");

        $request->validate([
            "observation.$documentId" => 'required|string',
        ], [
            "observation.$documentId.required" => 'La observación no puede estar vacía.',
        ]);

        if (!$observationText) {
            return redirect()->back()
                ->withErrors(["observation.$documentId" => 'La observación no puede estar vacía.'])
                ->withInput();
        }

        DighObservation::updateOrCreate(
            ['project_id' => $project->id, 'document_id' => $documentId],
            ['observation' => $observationText, 'origen' => 1]
        );

        // Verificar si ya existe un estado 14 para este proyecto
        $hasStage14 = ProjectStatus::where('project_id', $project->id)
                        ->where('stage_id', 14)
                        ->exists();

        if (!$hasStage14) {
            ProjectStatus::create([
                'project_id' => $project->id,
                'stage_id' => 14,
                'user_id' => auth()->id(),
                'record' => 'OBSERVACION DIGH'
            ]);
        }

        return redirect()->back()->with("success_{$documentId}", 'Observación guardada.');
    }


    public function saveDSGOObservation(Request $request, Project $project)
    {
        $documentId = $request->input('document_id');
        $observationText = $request->input("observation.$documentId");

        $request->validate([
            "observation.$documentId" => 'required|string',
        ], [
            "observation.$documentId.required" => 'La observación no puede estar vacía.',
        ]);

        if (!$observationText) {
            return redirect()->back()
                ->withErrors(["observation.$documentId" => 'La observación no puede estar vacía.'])
                ->withInput();
        }

        DighObservation::updateOrCreate(
            ['project_id' => $project->id, 'document_id' => $documentId],
            ['observation' => $observationText, 'origen' => 3]
        );

        $hasStage17 = ProjectStatus::where('project_id', $project->id)
                        ->where('stage_id', 17)
                        ->exists();

        if (!$hasStage17) {
            ProjectStatus::create([
                'project_id' => $project->id,
                'stage_id' => 17,
                'user_id' => auth()->id(),
                'record' => 'OBSERVACION DSGO'
            ]);
        }

        return redirect()->back()->with("success_{$documentId}", 'Observación guardada.');
    }



    // public function showDSGO(Project $project)
    // {
    //    //$this->authorize('admin.project.show', $project);
    //    $id=$project->id;
    //    $project_type= Land_project::where('land_id',$project->land_id)->first();
    //    $postulantes = ProjectHasPostulantes::where('project_id', $id)->get();
    //    $docproyecto = Assignment::where('project_type_id',$project_type->project_type_id)
    //    ->where('category_id',3)
    //    ->get();
    //    $history = ProjectStatus::where('project_id',$project['id'])
    //                ->orderBy('created_at')
    //                ->get();

    //                // Verificar si se ha cargado un archivo para cada elemento
    //    $uploadedFiles = [];
    //    foreach ($docproyecto as $item) {
    //        $uploadedFile = Documents::where('project_id', $project->id)
    //            ->where('document_id', $item->document_id)
    //            ->first();
    //        //return $uploadedFile;
    //        $documentExists = /*$uploadedFile &&*/ $uploadedFile  ? $uploadedFile->file_path : false;
    //        //return $documentExists;
    //        $uploadedFiles[$item->document_id] = $documentExists;
    //    }

    //    //return $history;

    //    return view('admin.project.DSGO.show', compact('project', 'docproyecto','history', 'postulantes','uploadedFiles'));
    // }


    public function showDSGO(Project $project)
    {
        //$this->authorize('admin.project.show', $project);
        $id = $project->id;

        // Obtener tipo de proyecto
        $project_type = Land_project::where('land_id', $project->land_id)->first();

        // Postulantes del proyecto
        $postulantes = ProjectHasPostulantes::where('project_id', $id)->get();

        // Documentos del proyecto
        $docproyecto = Assignment::where('project_type_id', $project_type->project_type_id)
            ->where('category_id', 2)
            ->get();

        // Historial del proyecto
        $history = ProjectStatus::where('project_id', $project['id'])
            ->orderBy('created_at')
            ->get();

        // Verificar si se ha cargado un archivo para cada documento
        $uploadedFiles = [];
        foreach ($docproyecto as $item) {
            $uploadedFile = Documents::where('project_id', $project->id)
                ->where('document_id', $item->document_id)
                ->first();

            $uploadedFiles[$item->document_id] = $uploadedFile ? $uploadedFile->file_path : false;
        }

        // 🔹 SOLO observaciones con origen = 2
        $observations = DighObservation::where('project_id', $project->id)
            ->where('origen', 4)
            ->pluck('observation', 'document_id'); // [document_id => 'observación']

        return view('admin.project.DSGO.show', compact(
            'project',
            'docproyecto',
            'history',
            'postulantes',
            'uploadedFiles',
            'observations'
        ));
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
        $dependencia = Auth::user()->rol_app->dependency_id;
        $email = Auth::user()->email;
        // $stages = Stage::where('id','!=',$project->getEstado->getStage->id)->get();

        switch ($estado) {
            case 1:
                // Lógica específica para el estado 1
                $stages = Stage::whereIn('id',[2])->get();
                // $opcion = 2;
                break;
            case 2:
                // Lógica específica para el estado 2
                $stages = Stage::whereIn('id', [3, 4, 6, 21])->get();
                break;
            case 3:
                    // Lógica específica para el estado 3
                    $stages = Stage::whereIn('id', [2, 7, 8])->get();
                    break;
            case 4:
                    // Lógica específica para el estado 3
                    $stages = Stage::whereIn('id', [3,21])->get();
                    break;
            case 5:
                 // Lógica específica para el estado 5
                 $stages = Stage::whereIn('id', [3, 4, 6])->get();
                 break;

            case 6:
            // Lógica específica para el estado 6
            $stages = Stage::whereIn('id', [3, 4])->get();
            break;

            case 8:
                 // Lógica específica para el estado 8
                 $stages = Stage::whereIn('id', [2, 9])->get();
                 break;
            case 9:
                 // Lógica específica para el estado 9
                 $stages = Stage::whereIn('id', [10])->get();
                 break;
            case 11:
                 // Lógica específica para el estado 11
                 if ($dependencia==1){
                    $stages = Stage::whereIn('id', [12])->get();
                 break;
                 }

                 $stages = Stage::whereIn('id', [13])->get();
                 break;
            case 12:
                 // Lógica específica para el estado 12
                 $stages = Stage::whereIn('id', [13,14,15])->get();
                 break;
            case 13:
                    // Lógica específica para el estado 13
                    $stages = Stage::whereIn('id', [16])->get();
                    break;
            case 16:
                     // Lógica específica para el estado 16
                     $stages = Stage::whereIn('id', [18,19])->get();
                     break;
            case 17:
                     // Lógica específica para el estado 17
                     $stages = Stage::whereIn('id', [18])->get();
                     break;
            case 21:
                     // Lógica específica para el estado 17
                     $stages = Stage::whereIn('id', [22])->get();
                     break;

        }

        $mensaje = 'Este cambio de estado quedara registrado en el historial del Proyecto';

        return view('admin.project.transition', compact('project', 'user','mensaje','stages','email', 'estado', 'dependencia'));

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

    public function notificar(Project $project)
    {
        $project->getEstado->getStage->id;
        $estado=$project->getEstado->getStage->id;
        $user = Auth::user()->id;
        $email = Auth::user()->email;
        // $stages = Stage::where('id','!=',$project->getEstado->getStage->id)->get();

        $mensaje = 'Esta notificacion quedara registrada en el historial del Proyecto';

        return view('admin.project.notificar', compact('project', 'user','mensaje','email', 'estado'));

    }

    public function historial($id)
    {
        $project = Project::findOrFail($id);

        $history = ProjectStatusF::where('project_id', $project->id)
            ->orderBy('created_at')
            ->with('imagen')
            ->get();

        // Contador específico para estado 1
        $state1Count = 0;

        $history = $history->map(function ($item) use (&$state1Count) {
            if ($item->stage_id == 1) {
                $state1Count++;

                if ($state1Count == 1 || $item->stage_id == 1) {
                    // Primera vez -> SAT
                    $user = User::find($item->user_id);
                    $item->nombre_usuario = $user ? $user->name . ' (SAT)' : '';
                } else {
                    // Desde la segunda vez en adelante -> Admin
                    $adminUser = \App\Models\AdminUser::find($item->user_id);
                    $item->nombre_usuario = $adminUser ? $adminUser->first_name . ' ' . $adminUser->last_name : '';
                }
            } elseif (in_array($item->stage_id, [5, 8, 11])) {
                // Siempre SAT
                $user = User::find($item->user_id);
                $item->nombre_usuario = $user ? $user->name . ' (SAT)' : '';
            } else {
                // Siempre Admin
                $adminUser = \App\Models\AdminUser::find($item->user_id);
                $item->nombre_usuario = $adminUser ? $adminUser->first_name . ' ' . $adminUser->last_name : '';
            }

            return $item;
        });

        $title = "HISTORIAL";

        return view('admin.project.historial', compact('title', 'history', 'project'));
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

        $sat = Sat::whereNotNull('NucRuc')
                    ->where('NucEst', '=', 'H')
                    ->select('NucNomSat', DB::raw('LTRIM(RTRIM(NucCod)) as NucCod'), 'NucCont')
                    ->get();



        $modalidad = Modality::all();
        $tierra = Land::all();
        $tipologias = Typology::all();

        $dep = [18, 19, 20, 21, 999];

        // Solo departamentos, sin localidades
        $departamentos = Departamento::whereNotIn('DptoId', $dep)
                        ->orderBy('DptoNom', 'asc')
                        ->get();

        return view('admin.project.edit', [
            'project' => $project,
            'sat' => $sat,
            'modalidad' => $modalidad,
            'tierra' => $tierra,
            'tipologias' => $tipologias,
            'departamentos' => $departamentos,
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

         // Reemplazar sat_id con el código real
        $sanitized['sat_id'] = $request->getSatId();
        $sanitized['modalidad_id'] = $request->getModalidadId();
        $sanitized['land_id'] = $request->getLandId();
        $sanitized['typology_id'] = $request->getTypologyId();

        // Agregar state_id y city_id igual que los anteriores
        $sanitized['state_id'] = $request->getStateId();
        $sanitized['city_id'] = $request->getCityId();

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

    function descargarDocumento($project, $document_id, $file_name)
    {
        //dd($project, $document_id, $file_name);
        //Esto es para descargar del disco remoto
        // return Storage::disk('remote')->download('uploads/' . $project . "/faltantes/" . $document_id . "/" . $file_name);
        return Storage::disk('local')->download('uploads/' . $project . "/faltantes/" . $document_id . "/" . $file_name);

        //Esto es para descargar del disco local
        // return Storage::disk('local')->download('uploads/' . $project . "/" . $document_id . "/" . $file_name);

        //return Storage::disk('remote')->download('uploads/1945/1/17082872871374512236.pdf');
        //Storage::disk('remote')->download('uploads/1945/1/17082872871374512236.pdf');
        /*$file = Storage::disk('remote')->get($file_name);
        //return Storage::disk('remote')->download($file_name);
        return (new Response($file, 200))
            ->header('Content-Type', '*');*/
    }

    function downloadFile($project, $document_id, $file_name)
    {
       // return "Bajar archivos";
        //Esto es para descargar del disco remoto
        // return Storage::disk('remote')->download('uploads/' . $project . "/" . $document_id . "/" . $file_name);
        return Storage::disk('local')->download('uploads/' . $project . "/" . $document_id . "/" . $file_name);

        //Esto es para descargar del disco local
        // return Storage::disk('local')->download('uploads/' . $project . "/" . $document_id . "/" . $file_name);

        //return Storage::disk('remote')->download('uploads/1945/1/17082872871374512236.pdf');
        //Storage::disk('remote')->download('uploads/1945/1/17082872871374512236.pdf');
        /*$file = Storage::disk('remote')->get($file_name);
        //return Storage::disk('remote')->download($file_name);
        return (new Response($file, 200))
            ->header('Content-Type', '*');*/
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

    public function project($id)
    {
        $project = Project::find($id);

        if (!$project) {
            return view('admin.project.project', [
                'project' => null,
                'projectNotFound' => true,
                'title' => 'Proyecto no encontrado',
                'tipoproy' => null,
                'postulantes' => collect(),
            ]);
        }

        $postulantes = ProjectHasPostulantes::where('project_id', $id)->get();
        $title = "Resumen Proyecto " . ($project->name ?? 'SIN NOMBRE');
        $tipoproy = Land_project::where('land_id', $project->land_id)->first();

        return view('admin.project.project', compact('project', 'title', 'tipoproy', 'postulantes'))
            ->with('projectNotFound', false);
    }

    public function crearmiembro(Request $request, $project, $postulante)
    {
        // return $request;
        // Obtener el estado del proyecto
        $proyectoEstado = ProjectStatus::where('project_id', $project)->latest()->first();
        // $ultimoEstado = $proyectoEstado->stage_id ?? null;

        // Validar la cédula
        if (!$request->filled('cedula')) {
            return redirect()->back()->with('error', 'Ingrese Cédula');
        }

        $cedula = $request->input('cedula');

        // Verifica restricciones generales
        if ($msg = $this->verificarRestriccionesGenerales($cedula)) {
            return redirect()->back()->with('status', $msg);
        }

        // Consulta datos desde el SII
        $datosPersona = $this->obtenerDatosPersona($cedula);
        if (isset($datosPersona['error'])) {
            return redirect()->back()->with('status', $datosPersona['error']);
        }

        // Extraer variables individuales
        $nombre = $datosPersona['nombre'] ?? '';
        $apellido = $datosPersona['apellido'] ?? '';
        $fecha = $datosPersona['fecha'] ?? '';
        $sexo = $datosPersona['sexo'] ?? '';
        $nac = $datosPersona['nac'] ?? '';
        $est = $datosPersona['est'] ?? '';

        $title = "Agregar Miembro Familiar";
        $project_id = Project::find($project);
        $nroexp = $cedula;

        $par = [1, 8];
        $parentesco = Parentesco::whereIn('id', $par)->orderBy('name', 'asc')->get();
        // if ($ultimoEstado === null) {
        //     $parentesco = Parentesco::whereIn('id', $par)->orderBy('name', 'asc')->get();
        // } else {
        //     $parentesco = Parentesco::all();
        // }

        $discapacdad = Discapacidad::all();
        $idpostulante=$postulante;

        return view('admin.postulante.ficha.createmiembro', compact(
            'nroexp', 'cedula', 'nombre', 'apellido', 'fecha', 'sexo',
            'nac', 'est', 'title', 'project_id',
            'discapacdad', 'parentesco', 'idpostulante'
        ));
    }

    public function crearmiembroNoCge(Request $request, $project, $postulante)
    {
        // return $request;
        // Obtener el estado del proyecto
        $proyectoEstado = ProjectStatus::where('project_id', $project)->latest()->first();
        // $ultimoEstado = $proyectoEstado->stage_id ?? null;

        // Validar la cédula
        if (!$request->filled('cedula')) {
            return redirect()->back()->with('error', 'Ingrese Cédula');
        }

        $cedula = $request->input('cedula');

        // Verifica restricciones generales
        if ($msg = $this->verificarRestriccionesGenerales($cedula)) {
            return redirect()->back()->with('status', $msg);
        }

        // Consulta datos desde el SII
        $datosPersona = $this->obtenerDatosPersona($cedula);
        if (isset($datosPersona['error'])) {
            return redirect()->back()->with('status', $datosPersona['error']);
        }

        // Extraer variables individuales
        $nombre = $datosPersona['nombre'] ?? '';
        $apellido = $datosPersona['apellido'] ?? '';
        $fecha = $datosPersona['fecha'] ?? '';
        $sexo = $datosPersona['sexo'] ?? '';
        $nac = $datosPersona['nac'] ?? '';
        $est = $datosPersona['est'] ?? '';

        $title = "Agregar Miembro Familiar";
        $project_id = Project::find($project);
        $nroexp = $cedula;

        $par = [1, 8];
        $parentesco = Parentesco::whereNotIn('id', $par)->orderBy('name', 'asc')->get();
        // if ($ultimoEstado === null) {
        //     $parentesco = Parentesco::whereIn('id', $par)->orderBy('name', 'asc')->get();
        // } else {
        //     $parentesco = Parentesco::all();
        // }

        $discapacdad = Discapacidad::all();
        $idpostulante=$postulante;

        return view('admin.postulante.ficha.createmiembro', compact(
            'nroexp', 'cedula', 'nombre', 'apellido', 'fecha', 'sexo',
            'nac', 'est', 'title', 'project_id',
            'discapacdad', 'parentesco', 'idpostulante'
        ));
    }

    private function verificarRestriccionesGenerales($cedula)
    {
        // Verificar si existe un expediente
        $expediente = SIG005::where('NroExpPer', $cedula)
            ->where('TexCod', 118)
            ->orderBy('NroExp', 'desc')
            ->first();
        if ($expediente) {
            // Verificar que el archivo esté en estado C o H
            $archivo = SIG006::where('NroExp', $expediente->NroExp)
                ->whereIn('DEExpEst', ['C', 'H'])
                ->first();


            if (!$archivo) {
                return 'Ya existe expendiente de FICHA DE PRE-INSCRIPCION FONAVIS-SVS!!!.';
            }
        }

        // Evaluar todas las restricciones, se haya pasado o no la condición de expediente
        if (Postulante::where('cedula', $cedula)->exists()) {
            return 'Ya existe el postulante!';
        }
        // $shmcer=SHMCER::where('CerPosCod', $cedula)->whereNotIn('CerEst', [2, 7, 8, 12])->exists();
        // dd($shmcer);
        if (SHMCER::where('CerPosCod', $cedula)->whereNotIn('CerEst', [2, 7, 8, 12])->exists()) {
            return 'Ya cuenta con certificado de Subsidio como Titular!';
        }
        // $shmcerCge=SHMCER::where('CerCoCI', $cedula)->whereNotIn('CerEst', [2, 7, 8, 12])->exists();
        // dd($shmcerCge);
        if (SHMCER::where('CerCoCI', $cedula)->whereNotIn('CerEst', [2, 7, 8, 12])->exists()) {
            return 'Ya cuenta con certificado de Subsidio como Conyuge!';
        }
        // $prmcli=PRMCLI::where('PerCod', $cedula)->where('PylCod', '!=', 'P.F.')->exists();
        // dd($prmcli);
        if (PRMCLI::where('PerCod', $cedula)->where('PylCod', '!=', 'P.F.')->exists()) {
            return 'Ya cuenta con Beneficios en la Institución!';
        }

        // $ivmsol=IVMSOL::where('SolPerCod', $cedula)->where('SolEtapa', 'B')->exists();
        // dd($ivmsol);
        if (IVMSOL::where('SolPerCod', $cedula)->where('SolEtapa', 'B')->exists()) {
            return 'Ya es Beneficiario Final!';
        }

        // $solicitante = IVMSOL::where('SolPerCge', $cedula)->first();
        // if ($solicitante) {
        //     $carterasol = PRMCLI::where('PerCod', trim($solicitante->SolPerCod))
        //         ->where('PylCod', '!=', 'P.F.')
        //         ->exists();

        //     if ($carterasol) {
        //         return 'Ya cuenta con Beneficios en la Institución como Conyuge!';
        //     }
        // }

        // $ivmsolcge = IVMSOL::where('SolPerCge', $cedula)->first();
        // // dd($ivmsolcge);
        // if ($ivmsolcge) {
        //         return 'Ya cuenta con Beneficios en la Institución como Conyuge!';
        // }

        if (IVMSOL::where('SolPerCge', $cedula)->where('SolEtapa', 'B')->exists()) {
            return 'Ya cuenta con Beneficios en la Institución como Conyuge!';
        }


        return null; // Alta permitida
    }

    private function obtenerDatosPersona($cedula)
    {
        try {
            $client = new \GuzzleHttp\Client();

            $auth = $client->post('https://sii.paraguay.gov.py/security', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
                'json' => [
                    'username' => 'muvhConsulta',
                    'password' => '*Sipp*2025**'
                ]
            ]);

            $tokenData = json_decode($auth->getBody()->getContents());

            if (empty($tokenData->success)) {
                throw new \Exception("API sin éxito");
            }

            $response = $client->get("https://sii.paraguay.gov.py/frontend-identificaciones/api/persona/obtenerPersonaPorCedula/{$cedula}", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $tokenData->token,
                    'Accept' => 'application/json'
                ]
            ]);

            $persona = json_decode($response->getBody()->getContents());

            if (!isset($persona->obtenerPersonaPorNroCedulaResponse->return)) {
                \Log::warning('API respondió pero sin datos válidos', ['cedula' => $cedula, 'respuesta' => $persona]);
                return null;
            }

            $p = $persona->obtenerPersonaPorNroCedulaResponse->return;

            if (!$p || isset($p->error)) {
                return null;
            }

            return [
                'nombre' => $p->nombres,
                'apellido' => $p->apellido,
                'cedula' => $p->cedula,
                'sexo' => $p->sexo,
                'fecha' => Carbon::parse($p->fechNacim)->toDateString(),
                'nac' => $p->nacionalidadBean ?? '',
                'est' => $p->estadoCivil ?? ''
            ];
        } catch (\Exception $e) {
            // Solo error de la API, no incluye fallo en BD
            \Log::warning('Error al obtener datos desde la API, intento con BD local', [
                'cedula' => $cedula,
                'mensaje' => $e->getMessage()
            ]);

            $persona = \App\Models\Persona::where('BDICed', $cedula)->first();

            if (!$persona) {
                \Log::error('No se encontró la persona en la BD local', ['cedula' => $cedula]);
                return null;
            }

            return [
                'nombre' => $persona->BDINom,
                'apellido' => $persona->BDIAPE,
                'cedula' => $persona->BDICed,
                'sexo' => $persona->BDISexo,
                'fecha' => $persona->BDIFecNac,
                'nac' => '',
                'est' => $persona->BDIEstCiv
            ];
        }
    }

    public function showpostulantes($id,$idpostulante)
    {
        // return "Postulantes lado ADM Show";
        $postulante=Postulante::find($idpostulante);
        $project = Project::find($id);
        $title="Resumen Postulante ";
        //dd($project);
        $tipoproy = Land_project::where('land_id',$project->land_id)->first();
        // $documentos = PostulantesDocuments::where('postulante_id',$idpostulante)->get();
        // $docproyecto = Assignment::where('project_type_id',$tipoproy->project_type_id)
        // ->whereNotIn('document_id', $documentos->pluck('document_id'))
        // ->where('category_id',2)
        // ->get();
        $miembros = PostulanteHasBeneficiary::where('postulante_id',$postulante->id)->get();
        //$docproyecto = $docproyecto->whereNotIn('document_id', $documentos->pluck('document_id'));
        return view('admin.postulante.show',compact('title','project','miembros','postulante'));
    }


}


