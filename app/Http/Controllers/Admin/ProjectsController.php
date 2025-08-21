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

    // Obtener los documentos no excluyentes faltantes
    $missingDocuments = [];
    foreach ($docproyectoNoExcluyentes as $item) {
        if (!isset($uploadedFiles1[$item->document_id]) || !$uploadedFiles1[$item->document_id]) {
            $missingDocuments[] = $item->document->name;
        }
    }

    return view('admin.project.show', compact('project', 'docproyecto', 'history', 'postulantes', 'uploadedFiles', 'docproyectoNoExcluyentes', 'docproyectoCondominio', 'uploadedFiles1', 'uploadedFiles2', 'missingDocuments'));
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
            ['observation' => $observationText]
        );

        $hasObservations = DighObservation::where('project_id', $project->id)->exists();

        if ($hasObservations) {
            // Obtenemos el último estado registrado del proyecto
            $lastStatus = ProjectStatus::where('project_id', $project->id)
                ->orderByDesc('id') // o orderBy('created_at', 'desc')
                ->first();

            if (!$lastStatus || $lastStatus->stage_id != 14) {
                ProjectStatus::create([
                    'project_id' => $project->id,
                    'stage_id' => 14,
                    'user_id' => auth()->id(),
                    'record' => 'OBSERVACION DIGH'
                ]);
            }
        }

        return redirect()->back()->with("success_{$documentId}", 'Observación guardada.');
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
                $stages = Stage::whereIn('id', [3, 4, 6, 1])->get();
                break;
            case 3:
                    // Lógica específica para el estado 3
                    $stages = Stage::whereIn('id', [2, 7, 8])->get();
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
                 $stages = Stage::whereIn('id', [12])->get();
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
                     $stages = Stage::whereIn('id', [17])->get();
                     break;
            case 17:
                     // Lógica específica para el estado 17
                     $stages = Stage::whereIn('id', [18])->get();
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
            ->get()
            ->map(function ($item) {
                if (in_array($item->stage_id, [1, 5, 8, 11])) {
                    $user = User::find($item->user_id);
                    $item->nombre_usuario = $user ? $user->name . ' (SAT)' : '';
                } else {
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



}


