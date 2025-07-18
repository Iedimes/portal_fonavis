<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\Land;
use App\Models\Project;
use App\Models\Departamento;
use App\Models\Distrito;
use App\Models\Sat;
use App\Models\Modality;
use App\Models\Document;
use App\Models\DocumentCheck;
use App\Models\Documents;
use App\Models\Documentsmissing;
use App\Models\Assignment;
use App\Models\Typology;
use App\Models\ProjectHasPostulantes;
use App\Models\Land_project;
use App\Models\ModalityHasLand;
use App\Models\Project_tipologies;
use App\Models\ProjectStatusF;
use App\Models\User;
use App\Models\AdminUser;
use App\Models\AdminUsersDependency;
use Symfony\Component\HttpFoundation\Response;
use PDF;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Http\Requests\StoreProject;
use Illuminate\Support\Facades\Mail;
// use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\File;

//use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{

    public $statesInit;

    public function __construct()
    {
        $this->middleware('auth');
        //$this->photos_path = public_path('/images');

        //$this->statesInit = State::all()->sortBy("name");

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = "Lista de Proyectos";

        $id = Auth::user()->id;
        $currentuser = User::find($id);

        $projects = Project::where('sat_id', trim($currentuser->sat_ruc))
            ->where('action', '=', null)
            ->get();

        //return $projects;
        //Mapper::map(-24.3697635, -56.5912129, ['zoom' => 6, 'type' => 'ROADMAP']);
        return view('projects.index', compact('projects', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $title = "Crear Proyecto";
        $tierra = Land::all();
        $modalidad = Modality::all();
        $dep = [18, 19, 20, 21, 999];
        $loc = [0, 900];
        //$departamentos = Departamento::all();
        $departamentos = Departamento::whereNotIn('DptoId', $dep)
                          ->orderBy('DptoNom', 'asc')->get();
        // $departamentos = Departamento::where('DptoId', '<', 18)
        //     ->orderBy('DptoNom', 'asc')->get();
        // $localidad = Distrito::all();

        $localidad = Distrito::whereNotIn('CiuId', $loc)
            ->orderBy('CiuNom', 'asc')->get();

        $tipologias = Typology::all();
        $id = Auth::user()->id;
        $user = User::find($id);

        //return $user->sat_ruc;
        //return $user->getSat->NucNomSat;
        return view('projects.create', compact('title', 'tierra', 'departamentos', 'localidad', 'modalidad', 'tipologias', 'user'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProject $request)
    {
        //
        //return 'store';
        //return $request;

        $sanitized = $request->getSanitized();

        $key = str_random(25);
        while (Project::where('certificate_pin', $key)->exists()) {
            $key = str_random(25);
        }
        $sanitized['certificate_pin'] = $key;

        //return $sanitized;

        $task = Project::create($sanitized);
        //Project::create($request->all());
        return redirect('projects/')->with('success', 'Se ha agregado un Nuevo Proyecto!');
        //return $request;
    }



    public function checkdocuments($id, $project_id, $sheets)
    {
        //return $sheets;
        $aux = DocumentCheck::where('project_id', $project_id)
            ->where('document_id', $id)
            ->first();

        if (!$aux) {
            $status = new DocumentCheck;
            $status->project_id = $project_id;
            $status->document_id = $id;
            $status->sheets = $sheets;
            $status->save();
            return "check creado!!";
        } else {
            $aux->delete();
            return "check eliminado";
        }




        return "controlador laravel con id: " . $id . " y proyecto: " . $project_id;
        //
        //return $request;
        //Project::create($request->all());
        //return redirect('projects/')->with('success', 'Se ha agregado un Nuevo Proyecto!');
        //return $request;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function show($id)
    // {
    //     $project=Project::find($id);
    //     $postulantes = ProjectHasPostulantes::where('project_id',$id)->get();
    //     //return $postulantes;
    //     $title="Resumen Proyecto ".$project->name;

    //     $tipoproy = Land_project::where('land_id',$project->land_id)->first();
    //     //dd($tipoproy);
    //     //$documentos = Documents::where('project_id',$id)->get();

    //     $docproyecto = Assignment::where('project_type_id',$tipoproy->project_type_id)
    //     //->whereNotIn('document_id', $documentos->pluck('document_id'))
    //     ->where('category_id',1)
    //     //->where('stage_id',1)
    //     ->get();
    //     $claves = $docproyecto->pluck('document_id');
    //     $history = ProjectStatus::where('project_id',$project['id'])
    //                 ->orderBy('created_at')
    //                 ->get();
    //     //return $history;
    //     //return $docproyecto->pluck('document_id')->toArray();
    //     //dd($docproyecto);
    //     //$docproyecto = $docproyecto->whereNotIn('document_id', $documentos->pluck('document_id'));
    //     return view('projects.show',compact('title','project','docproyecto','tipoproy','claves','history','postulantes'));
    // }



public function show($id)
{
    // Buscar el proyecto
    $project = Project::findOrFail($id);

    $postulantes = ProjectHasPostulantes::where('project_id', $id)->get();
    $title = "Resumen Proyecto " . $project->name;

    $tipoproy = Land_project::where('land_id', $project->land_id)->first();

    $docproyecto = Assignment::where('project_type_id', $tipoproy->project_type_id)
        ->where('category_id', 1)
        ->where('stage_id', 1)
        ->get();

    $datosAdiciones = Assignment::where('project_type_id', $tipoproy->project_type_id)
        ->whereIn('category_id', [2, 3])
        ->where('stage_id', 1)
        ->get();

    $documentIds = $datosAdiciones->pluck('document_id');

    $documentos = Documents::where('project_id', $id)
        ->whereIn('document_id', $documentIds)
        ->get();

    $claves = $docproyecto->pluck('document_id');

    $history = ProjectStatusF::where('project_id', $project->id)
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

    $uploadedFiles2 = [];
    foreach ($documentos as $item) {
        $uploadedFile2 = Documents::where('project_id', $project->id)
            ->where('document_id', $item->document_id)
            ->first();
        $documentExists = $uploadedFile2 ? $uploadedFile2->file_path : false;
        $uploadedFiles2[$item->document_id] = $documentExists;
    }

    // Validación de edades generales
    $edadesPostulantes = $postulantes->map(function ($post) {
        return \Carbon\Carbon::parse($post->getPostulante->birthdate)->age;
    });

    // Validación específica para parentesco 1 (esposo/a) y 8 (concubino/a)
    $edadesConyuges = $postulantes->flatMap(function ($post) {
        return $post->getMembers->filter(function ($member) {
            return in_array((int) $member->parentesco_id, [1, 8]) &&
                   $member->getPostulante &&
                   $member->getPostulante->birthdate;
        })->map(function ($member) {
            return \Carbon\Carbon::parse($member->getPostulante->birthdate)->age;
        });
    });



    $todosCargados = true;
    foreach ($docproyecto as $item) {
        if (!isset($uploadedFiles[$item->document_id])) {
            $todosCargados = false;
            break;
        }
    }

    $existenDocumentos = $documentos->isNotEmpty();

    return view('projects.show', compact(
        'title',
        'project',
        'docproyecto',
        'tipoproy',
        'claves',
        'history',
        'postulantes',
        'uploadedFiles',
        'todosCargados',
        'existenDocumentos',
        'datosAdiciones',
        'documentos',
        'uploadedFiles2',
        'edadesPostulantes',
        'edadesConyuges'
    ));
}

public function showEliminados($id)
{
    $project = Project::find($id);
    $postulantes = ProjectHasPostulantes::where('project_id', $id)->get();
    $title = "Resumen Proyecto " . $project->name;

    $tipoproy = Land_project::where('land_id', $project->land_id)->first();

    $docproyecto = Assignment::where('project_type_id', $tipoproy->project_type_id)
        ->where('category_id', 1)
        ->where('stage_id', 1)
        ->get();

    // Documentación técnica
    $datosAdiciones = Assignment::where('project_type_id', $tipoproy->project_type_id)
        ->whereIn('category_id', [2,3])
        ->where('stage_id', 1)
        ->get();

    $documentIds = $datosAdiciones->pluck('document_id');

    $documentos = Documents::where('project_id', $id)
        ->whereIn('document_id', $documentIds)
        ->get();

    $claves = $docproyecto->pluck('document_id');

    $history = ProjectStatusF::where('project_id', $project['id'])
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
    $uploadedFiles2 = [];
    foreach ($documentos as $item) {
        $uploadedFile2 = Documents::where('project_id', $project->id)
            ->where('document_id', $item->document_id)
            ->first();
        $documentExists = $uploadedFile2 ? $uploadedFile2->file_path : false;
        $uploadedFiles2[$item->document_id] = $documentExists;
    }

    // Verificar si todos los documentos están cargados
    $todosCargados = true;
    foreach ($docproyecto as $item) {
        if (!isset($uploadedFiles[$item->document_id])) {
            $todosCargados = false;
            break;
        }
    }

    $existenDocumentos = $documentos->isNotEmpty();

    return view('projects.showEliminados', compact('title', 'project', 'docproyecto', 'tipoproy', 'claves', 'history', 'postulantes', 'uploadedFiles', 'todosCargados', 'existenDocumentos', 'datosAdiciones', 'documentos', 'uploadedFiles2'));
}

    public function showDoc($id)
    {
        $project = Project::find($id);
        $documents = Documentsmissing::where('project_id', $id)->get();
        $postulantes = ProjectHasPostulantes::where('project_id', $id)->get();
        $title = "Resumen Proyecto " . $project->name;

        $tipoproy = Land_project::where('land_id', $project->land_id)->first();

        $docproyecto = Assignment::where('project_type_id', $tipoproy->project_type_id)
            ->where('category_id', 1)
            ->where('stage_id', 1)
            ->get();

        $claves = $docproyecto->pluck('document_id');

        $history = ProjectStatusF::where('project_id', $project['id'])
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

        //return $uploadedFiles;
        //return Storage::disk('local');
        //return Storage::disk('remote')->download('uploads/1945/1/17082872871374512236.pdf');
        //return $data;
        // Verificar si todos los documentos están cargados
        $todosCargados = true;
        foreach ($docproyecto as $item) {
            if (!isset($uploadedFiles[$item->document_id])) {
                $todosCargados = false;
                break;
            }
        }

        return view('projects.showDocumento', compact('title', 'project', 'docproyecto', 'tipoproy', 'claves', 'history', 'postulantes', 'uploadedFiles', 'todosCargados', 'documents'));
    }


    public function showDocTec($id)
    {
        $project = Project::find($id);
        $postulantes = ProjectHasPostulantes::where('project_id', $id)->get();
        $title = "Resumen Proyecto " . $project->name;

        $tipoproy = Land_project::where('land_id', $project->land_id)->first();

        $docproyecto = Assignment::where('project_type_id', $tipoproy->project_type_id)
            ->whereIn('category_id', [2])
            ->where('stage_id', 1)
            ->get();

        $claves = $docproyecto->pluck('document_id');

        $history = ProjectStatusF::where('project_id', $project['id'])
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


        $todosCargados = true;
        foreach ($docproyecto as $item) {
            if (!isset($uploadedFiles[$item->document_id])) {
                $todosCargados = false;
                break;
            }
        }

        $hayDocumentoFaltante = !$todosCargados; // Verifica si hay algún documento faltante


        return view('projects.showDocTec', compact('title', 'project', 'docproyecto', 'tipoproy', 'claves', 'history', 'postulantes', 'uploadedFiles', 'todosCargados', 'hayDocumentoFaltante'));
    }

    public function showDocNoExcluyentes($id)
    {
        $project = Project::find($id);
        $postulantes = ProjectHasPostulantes::where('project_id', $id)->get();
        $title = "Resumen Proyecto " . $project->name;

        $tipoproy = Land_project::where('land_id', $project->land_id)->first();

        $docproyecto = Assignment::where('project_type_id', $tipoproy->project_type_id)
            ->whereIn('category_id', [4])
            ->where('stage_id', 1)
            ->get();

        $claves = $docproyecto->pluck('document_id');

        $history = ProjectStatusF::where('project_id', $project['id'])
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


        $todosCargados = true;
        foreach ($docproyecto as $item) {
            if (!isset($uploadedFiles[$item->document_id])) {
                $todosCargados = false;
                break;
            }
        }

        $hayDocumentoFaltante = !$todosCargados; // Verifica si hay algún documento faltante


        return view('projects.showDocNoExcluyentes', compact('title', 'project', 'docproyecto', 'tipoproy', 'claves', 'history', 'postulantes', 'uploadedFiles', 'todosCargados', 'hayDocumentoFaltante'));
    }


    public function showDocCondominio($id)
    {
        $project = Project::find($id);
        $postulantes = ProjectHasPostulantes::where('project_id', $id)->get();
        $title = "Resumen Proyecto " . $project->name;

        $tipoproy = Land_project::where('land_id', $project->land_id)->first();

        $docproyecto = Assignment::where('project_type_id', $tipoproy->project_type_id)
            ->whereIn('category_id', [5])
            ->where('stage_id', 1)
            ->get();

        $claves = $docproyecto->pluck('document_id');

        $history = ProjectStatusF::where('project_id', $project['id'])
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


        $todosCargados = true;
        foreach ($docproyecto as $item) {
            if (!isset($uploadedFiles[$item->document_id])) {
                $todosCargados = false;
                break;
            }
        }

        $hayDocumentoFaltante = !$todosCargados; // Verifica si hay algún documento faltante


        return view('projects.showDocCondominio', compact('title', 'project', 'docproyecto', 'tipoproy', 'claves', 'history', 'postulantes', 'uploadedFiles', 'todosCargados', 'hayDocumentoFaltante'));
    }

    public function showProyMiembros($id)
    {
        try {
            // Obtener todos los postulantes del proyecto
            $postulantes = ProjectHasPostulantes::where('project_id', $id)->get();

            // Validar edades de postulantes
            $edadesPostulantes = $postulantes->map(function ($post) {
                return $post->getPostulante && $post->getPostulante->birthdate
                    ? \Carbon\Carbon::parse($post->getPostulante->birthdate)->age
                    : null;
            })->filter();

            if ($edadesPostulantes->contains(fn($edad) => $edad < 18)) {
                return response()->json(['message' => 'Existe al menos un postulante menor de 18 años. No se puede enviar el proyecto.'], 400);
            }

            // Validar edades de cónyuges
            $edadesConyuges = $postulantes->flatMap(function ($post) {
                return $post->getMembers->filter(function ($member) {
                    return in_array((int) $member->parentesco_id, [1, 8]) &&
                        $member->getPostulante &&
                        $member->getPostulante->birthdate;
                })->map(function ($member) {
                    return \Carbon\Carbon::parse($member->getPostulante->birthdate)->age;
                });
            });

            if ($edadesConyuges->contains(fn($edad) => $edad < 18)) {
                return response()->json(['message' => 'Existe al menos un cónyuge menor de 18 años. No se puede enviar el proyecto.'], 400);
            }

            // Guardar estado del proyecto
            $state = new ProjectStatusF();
            $state->project_id = $id;
            $state->stage_id = '8';
            $state->user_id = Auth::user()->id;
            $state->record = 'GRUPO FAMILIAR ENVIADO!';
            $state->save();

            // Enviar correo electrónico
            $projecto = Project::where('id', $id)->first();
            $sat = $projecto->sat_id;
            $satnombre = Sat::where('NucCod', $sat)->first();

            $dependenciaDGSO = AdminUsersDependency::where('dependency_id', 3)
                                                    ->pluck('admin_user_id');

            $usuarios = AdminUser::whereIn('id', $dependenciaDGSO)->get();
            $userEmails = $usuarios->pluck('email')->toArray();
            $toEmails = ['preseleccionfonavis@muvh.gov.py'];
            $allEmails = array_merge($userEmails, $toEmails);

            $subject = 'GRUPO FAMILIAR ENVIADO';

            Mail::send('admin.project-status.emailSISASGOFONAVIS', [
                'proyecto' => $projecto->name,
                'id' => $projecto->id,
                'sat' => $sat,
                'satnombre' => $satnombre->NucNomSat
            ], function ($message) use ($allEmails, $subject) {
                $message->to($allEmails);
                $message->subject($subject);
                $message->from('sistema_fonavis@muvh.gov.py', 'DGTIC - MUVH');
            });

            return response()->json(['message' => 'Grupo Familiar enviado exitosamente!!!']);

        } catch (\Exception $e) {
            throw new \Exception('No se pudo enviar el correo electrónico: ' . $e->getMessage());
        }
    }

public function showTecnico($id)
{
    //return "Cambiar de estado";
    try {
        $state = new ProjectStatusF();
        $state->project_id = $id;
        $state->stage_id = '11';
        $state->user_id = Auth::user()->id;
        $state->record = 'DOCUMENTACION TECNICA ENVIADA!';
        $state->save();

        //return "controlamos si inserta bien";

        // Enviar correo electrónico

        $projecto = Project::where('id', $id)->get();
        $sat = $projecto[0]->sat_id;
        // $useremail = User::where('sat_ruc', $sat)->get()->first();
        $satnombre = Sat::where('NucCod', $sat)->get()->first();

        $useremail = 'preseleccionfonavis@muvh.gov.py';
        $subject = 'DOCUMENTACION TECNICA ENVIADA';

        // Crear un array para almacenar las direcciones de correo electrónico
        $toEmails = [];

        if ($useremail) {
            $toEmails[] = $useremail; // Mail de FONAVIS
        }

        // Agregar otras direcciones de correo duro
        //$toEmails[] = 'preseleccionfonavis@muvh.gov.py'; // correo FONAVIS
        //$toEmails[] = 'nmorel@muvh.gov.py'; // correo FONAVIS - DGSO DESPUES HAY QUE CAMBIAR POR EL QUE CORRESPONDE

        // Mail::send('admin.project-status.emailDGF', ['nombre' => $nombre, 'lider' => $lider, 'sat' => $nombre_sat, 'modalidad' => $modalidad_nombre, 'terreno' => $terreno, 'departamento' => $dto, 'project' => $project, 'distrito' => $distrito], function ($message) use ($toEmails, $subject) {
            Mail::send('admin.project-status.emailSATFONAVIS', ['proyecto' => $projecto[0]->name ,'id' => $projecto[0]->id,'sat' => $sat,'satnombre' => $satnombre], function ($message) use ($toEmails, $subject) {
            $message->to($toEmails);
            $message->subject($subject);
            $message->from('sistema_fonavis@muvh.gov.py', 'DGTIC - MUVH');
        });

        //return response()->json(['message' => 'Documentacion técnica enviada exitosamente!!!']);
         return [
             'message' => 'success'
         ];
        return response()->json(['message' => 'success']);

    } catch (\Exception $e) {

        throw new \Exception('No se pudo enviar el correo electrónico: ' . $e->getMessage());
    }
}




    function downloadFile($project, $document_id, $file_name)
    {
       // return "Bajar archivos";
        //Esto es para descargar del disco remoto
        // return Storage::disk('remote')->download('uploads/' . $project . "/" . $document_id . "/" . $file_name);

        //Esto es para descargar del disco local
        return Storage::disk('local')->download('uploads/' . $project . "/" . $document_id . "/" . $file_name);

        //return Storage::disk('remote')->download('uploads/1945/1/17082872871374512236.pdf');
        //Storage::disk('remote')->download('uploads/1945/1/17082872871374512236.pdf');
        /*$file = Storage::disk('remote')->get($file_name);
        //return Storage::disk('remote')->download($file_name);
        return (new Response($file, 200))
            ->header('Content-Type', '*');*/
    }

    function bajarDocumento($project, $document_id, $file_name)
    {
        //dd($project, $document_id, $file_name);
        //Esto es para descargar del disco remoto
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

    public function generatePDF($id)
    {
        $project = Project::find($id);
        $tipoproy = Land_project::where('land_id', $project->land_id)->first();
        $docproyecto = Assignment::where('project_type_id', $tipoproy->project_type_id)
            ->where('category_id', 1)
            ->get();

        $codigoQr = QrCode::size(150)->generate(config('app.url') . '/' . $project->certificate_pin);

        $data = [
            'title' => 'Welcome to HDTuto.com',
            'project' => $project,
            'documents' => $docproyecto,
            'valor' => $codigoQr,
        ];

        $pdf = PDF::loadView('myPDF', $data);

        return $pdf->download('FORMULARIO-INGRESO-' . $project->name . '.pdf');
    }


    public function verification($key)
    {

        //return $key;
        $project = Project::where('certificate_pin', $key)
            //->select('name', 'last_name', 'government_id', 'farm', 'account', 'amount', 'state_id', 'city_id', 'created_at')
            ->first();

        //return $task;
        return view('verification', compact('project'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $title = "Editar Proyecto";
        $tierra = Land::all();
        $modalidad = Modality::all();
        $dep = [2, 4, 5, 8, 10, 16, 22];
        $loc = [0, 900];
        $departamentos = Departamento::where('DptoId', '<', 18)
            ->orderBy('DptoNom', 'asc')->get();

        $localidad = Distrito::whereNotIn('CiuId', $loc)
            ->orderBy('CiuNom', 'asc')->get();

        $project = Project::find($id);
        //$cities = $this->distrito($project->state_id);
        //$cities = json_decode($cities, true);
        $tipologias = Typology::all();

        $lands = $this->lands($project->land_id);
        $lands = json_decode($lands, true);

        $typology = $this->typologyedit($project->typology_id);
        $typology = json_decode($typology, true);

        // $local = $this->localedit($project->localidad);
        // $local = json_decode($local, true);

        $id = Auth::user()->id;
        $user = User::find($id);
        return view('projects.edit', compact('title', 'tierra', 'typology', 'lands', 'departamentos', 'modalidad', 'project', 'tipologias', 'user', 'localidad'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreProject $request, $id)
    {
        //
        //return $id;
        $project = Project::find($id);
        $project->name = $request->input("name");
        $project->phone = $request->input("phone");
        $project->households = $request->input("households");
        $project->state_id = $request->input("state_id");
        $project->city_id = $request->input("city_id");
        $project->land_id = $request->input("land_id");
        $project->modalidad_id = $request->input("modalidad_id");
        $project->leader_name = $request->input("leader_name");
        $project->localidad = $request->input("localidad");
        $project->typology_id = $request->input("typology_id");
        $project->res_nro = $request->input("res_nro");
        $project->fechares = $request->input("fechares");
        $project->coordenadax = $request->input("coordenadax");
        $project->coordenaday = $request->input("coordenaday");
        $project->finca_nro = $request->input("finca_nro");
        $project->ubicacion = $request->input("ubicacion");

        $project->save();

        return redirect('projects')->with('success', 'El proyecto fue actualizado!');
    }


    public function upload(Request $request)
    {

        // Validación
        $this->validate($request, [
            // 'archivo' => 'required|max:100000|mimes:pdf',
            'archivo' => 'required|max:30720|mimes:pdf'
        ], [
            'archivo.required' => 'Debe seleccionar un archivo.',
            'archivo.max' => 'El archivo supera el tamaño definido.',
            'archivo.mimes' => 'El archivo debe ser de formato PDF.',
        ]);

        // Obtener ids
        $project_id = $request->project_id;
        $document_id = $request->document_id;

        // Ruta de carpetas
        $folder = "uploads/$project_id/$document_id";

        // Validar documento existente
        $exists = Documents::where('project_id', $project_id)
            ->where('document_id', $document_id)
            ->first();

        if ($exists) {
            return redirect("/projects/$project_id")->withErrors('El documento ya existe');
        }

        // Obtener archivo
        $file = $request->file('archivo');

        // Generar nombre archivo
        $filename = time() . rand() . '.' . $file->getClientOriginalExtension();

        try {
            // $remoteDisk = Storage::disk('remote'); // Acceder al disco remoto

            // if (!$remoteDisk->exists($folder)) {
            //     $remoteDisk->makeDirectory($folder);
            // }

            // $remoteDisk->putFileAs($folder, $file, $filename);

            $localDisk = Storage::disk('local'); // Acceder al disco local

            if (!$localDisk->exists($folder)) {
                $localDisk->makeDirectory($folder);
            }

            $localDisk->putFileAs($folder, $file, $filename);
        } catch (\Exception $e) {
            return back()->withErrors('Error subiendo archivo');
        }

        // Guardar en BD
        $document = new Documents;

        $document->project_id = $request->project_id;
        $document->document_id = $request->document_id;
        $document->file_path = $filename;
        $document->title = $request->title;

        $document->save();

        return redirect("/projects/$project_id")
            ->with('message', 'Archivo subido');
    }

    public function uploadDocumento(Request $request)
    {
        // Validación
        $this->validate($request, [
            'archivos' => 'required|array|max:30720',


            'archivos.*' => 'file'
        ], [
            'archivos.required' => 'Debe seleccionar al menos un archivo.',
            'archivos.array' => 'Los archivos deben ser proporcionados en formato de array.',
            'archivos.*.file' => 'Uno o más archivos no son válidos.',
            'archivos.*.max' => 'El archivo supera el tamaño definido.'
        ]);

        // Obtener ids
        $project_id = $request->input('project_id');
        $archivos = $request->file('archivos');

        $archivosNombres = [];

        // // Obtener el último ID existente en la carpeta
        // $remoteDisk = Storage::disk('remote'); // Acceder al disco remoto
        // $lastId = 0;
        // $existingFolders = $remoteDisk->directories("uploads/$project_id/faltantes");
        // foreach ($existingFolders as $existingFolder) {
        //     $folderId = (int) basename($existingFolder);
        //     if ($folderId > $lastId) {
        //         $lastId = $folderId;
        //     }
        // }

        $localDisk = Storage::disk('local'); // Acceder al disco local
        $lastId = 0;
        $existingFolders = $localDisk->directories("uploads/$project_id/faltantes");
        foreach ($existingFolders as $existingFolder) {
            $folderId = (int) basename($existingFolder);
            if ($folderId > $lastId) {
                $lastId = $folderId;
            }
        }

    foreach ($archivos as $indice => $archivo) {
        $id = $lastId + $indice + 1; // Incrementa el último ID existente sumando el índice actual
        $nombre = $archivo->getClientOriginalName();

        // Ruta de carpetas
        $folder = "uploads/$project_id/faltantes/$id";

        try {
            $localDisk = Storage::disk('local'); // Acceder al disco remoto

            if (!$localDisk->exists($folder)) {
                $localDisk->makeDirectory($folder);
            }

            // Obtener nombre original del archivo
                $filename = $nombre;

            // Comprobar si existe un archivo con el mismo nombre
            $counter = 1;
            while ($localDisk->exists($folder . '/' . $filename)) {
                $filename = pathinfo($nombre, PATHINFO_FILENAME) . '_' . $counter . '.' . pathinfo($nombre, PATHINFO_EXTENSION);
                $counter++;
            }

            // // Generar nombre de archivo único
            //     $filename = time() . '_' . uniqid() . '.' . $archivo->getClientOriginalExtension();

            // Subir archivo con el nombre original o uno modificado si ya existe
                $localDisk->putFileAs($folder, $archivo, $filename);
                $archivosNombres[$id] = $nombre;




            // Guardar en BD

                 $document = new Documentsmissing;

                 $document->project_id = $project_id;
                 $document->document_id = $id;
                 $document->file_path = $filename;
                 $document->title = "DOCUMENTO FALTANTE. $id";

                 $document->save();

        } catch (\Exception $e) {
            return back()->withErrors('Error subiendo archivo');
        }
    }
            return redirect("/projectsDoc/$project_id")->with('message', 'Archivos subidos');
    }

    public function uploadTecnico(Request $request)
    {

        // Validación
        $this->validate($request, [
            // 'archivo' => 'required|max:100000|mimes:pdf',
            'archivo' => 'required|max:30720|mimes:pdf'
        ], [
            'archivo.required' => 'Debe seleccionar un archivo.',
            'archivo.max' => 'El archivo supera el tamaño definido..',
            'archivo.mimes' => 'El archivo debe ser de formato PDF.',
        ]);


        // Obtener ids
        $project_id = $request->project_id;
        $document_id = $request->document_id;

        // Ruta de carpetas
        $folder = "uploads/$project_id/$document_id";

        // Validar documento existente
        $exists = Documents::where('project_id', $project_id)
            ->where('document_id', $document_id)
            ->first();

        if ($exists) {
            return redirect("/projectsDocTec/$project_id")->withErrors('El documento ya existe');
        }

        // Obtener archivo
        $file = $request->file('archivo');

        // Generar nombre archivo
        $filename = time() . rand() . '.' . $file->getClientOriginalExtension();

        try {
            $localDisk = Storage::disk('local'); // Acceder al disco remoto

            if (!$localDisk->exists($folder)) {
                $localDisk->makeDirectory($folder);
            }

            $localDisk->putFileAs($folder, $file, $filename);

            // $localDisk = Storage::disk('local'); // Acceder al disco local

            // if (!$localDisk->exists($folder)) {
            //     $localDisk->makeDirectory($folder);
            // }

            // $localDisk->putFileAs($folder, $file, $filename);
        } catch (\Exception $e) {
            return back()->withErrors('Error subiendo archivo');
        }

        // Guardar en BD
        $document = new Documents;

        $document->project_id = $request->project_id;
        $document->document_id = $request->document_id;
        $document->file_path = $filename;
        $document->title = $request->title;

        $document->save();

        return redirect("/projectsDocTec/$project_id")
            ->with('message', 'Archivo subido');
    }

    public function uploadNoExcluyente(Request $request)
    {

        // Validación
        $this->validate($request, [
        //    'archivo' => 'required|max:100000|mimes:pdf',
        'archivo' => 'required|max:30720|mimes:pdf'

        ], [
            'archivo.required' => 'Debe seleccionar un archivo.',
            'archivo.max' => 'El archivo supera el tamaño definido.',
            'archivo.mimes' => 'El archivo debe ser de formato PDF.',
        ]);


        // Obtener ids
        $project_id = $request->project_id;
        $document_id = $request->document_id;

        // Ruta de carpetas
        $folder = "uploads/$project_id/$document_id";

        // Validar documento existente
        $exists = Documents::where('project_id', $project_id)
            ->where('document_id', $document_id)
            ->first();

        if ($exists) {
            return redirect("/projectsDocNoExcluyentes/$project_id")->withErrors('El documento ya existe');
        }

        // Obtener archivo
        $file = $request->file('archivo');

        // Generar nombre archivo
        $filename = time() . rand() . '.' . $file->getClientOriginalExtension();

        try {
            $localDisk = Storage::disk('local'); // Acceder al disco remoto

            if (!$localDisk->exists($folder)) {
                $localDisk->makeDirectory($folder);
            }

            $localDisk->putFileAs($folder, $file, $filename);

            // $localDisk = Storage::disk('local'); // Acceder al disco local

            // if (!$localDisk->exists($folder)) {
            //     $localDisk->makeDirectory($folder);
            // }

            // $localDisk->putFileAs($folder, $file, $filename);
        } catch (\Exception $e) {
            return back()->withErrors('Error subiendo archivo');
        }

        // Guardar en BD
        $document = new Documents;

        $document->project_id = $request->project_id;
        $document->document_id = $request->document_id;
        $document->file_path = $filename;
        $document->title = $request->title;

        $document->save();

        return redirect("/projectsDocNoExcluyentes/$project_id")
            ->with('message', 'Archivo subido');
    }


    public function uploadCondominio(Request $request)
    {

        // Validación
        $this->validate($request, [
        //    'archivo' => 'required|max:100000|mimes:pdf',
        'archivo' => 'required|max:30720|mimes:pdf'

        ], [
            'archivo.required' => 'Debe seleccionar un archivo.',
            'archivo.max' => 'El archivo supera el tamaño definido.',
            'archivo.mimes' => 'El archivo debe ser de formato PDF.',
        ]);


        // Obtener ids
        $project_id = $request->project_id;
        $document_id = $request->document_id;

        // Ruta de carpetas
        $folder = "uploads/$project_id/$document_id";

        // Validar documento existente
        $exists = Documents::where('project_id', $project_id)
            ->where('document_id', $document_id)
            ->first();

        if ($exists) {
            return redirect("/projectsDocCondominio/$project_id")->withErrors('El documento ya existe');
        }

        // Obtener archivo
        $file = $request->file('archivo');

        // Generar nombre archivo
        $filename = time() . rand() . '.' . $file->getClientOriginalExtension();

        try {
            $localDisk = Storage::disk('local'); // Acceder al disco remoto

            if (!$localDisk->exists($folder)) {
                $localDisk->makeDirectory($folder);
            }

            $localDisk->putFileAs($folder, $file, $filename);

            // $localDisk = Storage::disk('local'); // Acceder al disco local

            // if (!$localDisk->exists($folder)) {
            //     $localDisk->makeDirectory($folder);
            // }

            // $localDisk->putFileAs($folder, $file, $filename);
        } catch (\Exception $e) {
            return back()->withErrors('Error subiendo archivo');
        }

        // Guardar en BD
        $document = new Documents;

        $document->project_id = $request->project_id;
        $document->document_id = $request->document_id;
        $document->file_path = $filename;
        $document->title = $request->title;

        $document->save();

        return redirect("/projectsDocCondominio/$project_id")
            ->with('message', 'Archivo subido');
    }

    public function eliminar(Request $request, $project_id, $document_id)
    {

        // Obtener el documento por project_id y document_id
        $document = Documents::where('project_id', $project_id)->where('document_id', $document_id)->firstOrFail();

        // Eliminar el archivo físicamente del disco remoto
        //Storage::disk('remote')->delete("uploads/{$document->project_id}/{$document->document_id}/{$document->file_path}");

        // Eliminar el archivo físicamente del disco local
        Storage::disk('local')->delete("uploads/{$document->project_id}/{$document->document_id}/{$document->file_path}");

        // Eliminar el registro de la base de datos
        $document->delete();

        return redirect()->back()->with('message', 'Archivo eliminado correctamente');
    }

    public function eliminarDocumento(Request $request, $project_id, $document_id)
    {

        // Obtener el documento por project_id y document_id
        $document = Documentsmissing::where('project_id', $project_id)->where('document_id', $document_id)->firstOrFail();

        // Eliminar el archivo físicamente del disco remoto
        //Storage::disk('remote')->delete("uploads/{$document->project_id}/faltantes/{$document_id}/{$document->file_path}");

        // Eliminar el archivo físicamente del disco local
        Storage::disk('local')->delete("uploads/{$document->project_id}/{$document->document_id}/{$document->file_path}");

        // Eliminar el registro de la base de datos
        $document->delete();

        return redirect()->back()->with('message', 'Archivo eliminado correctamente');
    }

    public function enviarDocumentosFaltantes(Request $request)
    {
        $id = $request->project_id;

        try {
            $state = new ProjectStatusF();
            $state->project_id = $id;
            $state->stage_id = '5';
            $state->user_id = Auth::user()->id;
            $state->record = 'CON DOCUMENTACION DGJN!';
            $state->save();

            $projecto = Project::where('id', $request->project_id)->first();
            $sat = $projecto->sat_id;
            $satnombre = Sat::where('NucCod', $sat)->first();

            $dependenciaDGJN = AdminUsersDependency::where('dependency_id', 2)
                                                ->pluck('admin_user_id');

            $usuarios = AdminUser::whereIn('id', $dependenciaDGJN)->get();

            // Extraer los correos en un array
            $userEmails = $usuarios->pluck('email')->toArray();

            // Agregar el correo fijo
            $toEmails = ['preseleccionfonavis@muvh.gov.py'];

            // Combinar ambas listas de correos
            $allEmails = array_merge($userEmails, $toEmails);

            $subject = 'SAT REMITE DOCUMENTACION SOLICITADA';

            Mail::send('admin.project-status.emailSATDGJNFONAVIS', [
                'proyecto' => $projecto->name,
                'id' => $projecto->id,
                'sat' => $sat,
                'satnombre' => $satnombre->NucNomSat
            ], function ($message) use ($allEmails, $subject) {
                $message->to($allEmails);
                $message->subject($subject);
                $message->from('sistema_fonavis@muvh.gov.py', 'DGTIC - MUVH');
            });

        } catch (\Exception $e) {
            throw new \Exception('No se pudo enviar el correo electrónico: ' . $e->getMessage());
        }

        return redirect("/projectsDoc/$id")->with('message', 'Documentos faltantes enviados exitosamente');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function send(Request $request, $id)
    {

            $state = new ProjectStatusF();
            $state->project_id = $id;
            $state->stage_id = '1';
            $state->user_id = Auth::user()->id;
            $state->record = 'Proyecto Enviado!';
            $state->save();
            try {
            // // Enviar correo electrónico
            $project = Project::find($id);
            $sat_id = $project->sat_id;
            $sat_nombre = Sat::where('NucCod', $sat_id)->first();
            $nombre_sat = $sat_nombre->NucNomSat;
            $nombre = $project->name;
            $lider = $project->leader_name;
            $modalidad = Modality::where('id', $project->modalidad_id)->first();
            $modalidad_nombre = $modalidad->name;
            $tipo_terreno = Land::where('id', $project->land_id)->first();
            $terreno = $tipo_terreno->name;
            $departamento = Departamento::where('DptoId', $project->state_id)->first();
            $dto = $departamento->DptoNom;
            $ciudad = Distrito::where('CiuId', $project->city_id)->first();
            $destrito = $ciudad->CiuNom;
            $email = 'proyectos_ingresados@muvh.gov.py';
            $subject = 'PROYECTO INGRESADO';

            Mail::send('admin.project-status.emailDGF', ['nombre' => $nombre, 'lider' => $lider, 'sat' => $nombre_sat, 'modalidad' => $modalidad_nombre, 'terreno' => $terreno, 'departamento' => $dto, 'project' => $project, 'distrito' => $destrito], function ($message) use ($email, $subject) {
                    $message->to($email);
                    $message->subject($subject);
                    $message->from('sistema_fonavis@muvh.gov.py', 'DGTIC - MUVH');
                });

            return [
                    'message' => 'success'
                ];
            } catch (\Exception $e) {
                throw new \Exception('No se pudo enviar el correo electrónico: ' . $e->getMessage());
            }

        //  return [
        //               'message' => 'success'
        //           ];

    }

    public function destroyfile(Request $request)
    {
        //Documents::find($id)->delete();
        //return back()->with('error', 'Se ha eliminado el archivo!');
        //return $request;
        $file = Documents::find($request->delete_id);

        $file_path = $this->photos_path . '/' . $file->project_id . '/project/general/' . $file->file_path;
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        Documents::find($request->delete_id)->delete();
        return back()->with('error', 'Se ha eliminado el archivo!');
    }

    public function distrito($dptoid)
    {
        $dpto = Distrito::where('CiuDptoID', $dptoid)->get()->sortBy("CiuNom")->pluck("CiuNom", "CiuId");
        return json_encode($dpto, JSON_UNESCAPED_UNICODE);
    }


    // public function distritosinjson($dptoid){
    //     //$dpto =
    //     return Distrito::where('CiuDptoID', $dptoid)->get()->sortBy("CiuNom")->pluck("CiuNom","CiuId");
    //     //return json_encode($dpto, JSON_FORCE_OBJECT);
    //     //return json_encode($dpto , JSON_UNESCAPED_UNICODE);
    // }

    public function lands($dptoid)
    {
        $dpto = ModalityHasLand::join('lands', 'modality_has_lands.land_id', '=', 'lands.id')
            ->where('modality_id', $dptoid)->get()->sortBy("name")->pluck("name", "land_id");
        return json_encode($dpto, JSON_UNESCAPED_UNICODE);
    }

    public function typology($dptoid)
    {
        $tipo = Land_project::where('land_id', $dptoid)->first();
        //dd($tipo);
        $dpto = Project_tipologies::join('typologies', 'project_type_has_typologies.typology_id', '=', 'typologies.id')
            ->where('project_type_id', $tipo->project_type_id)->get()->sortBy("name")->pluck("name", "typology_id");
        return json_encode($dpto, JSON_UNESCAPED_UNICODE);
    }

    public function typologyedit($dptoid)
    {
        //$tipo = Land_project::where('land_id',$dptoid)->first();
        //dd($tipo);
        $dpto = Project_tipologies::join('typologies', 'project_type_has_typologies.typology_id', '=', 'typologies.id')
            ->where('typology_id', $dptoid)->get()->sortBy("name")->pluck("name", "typology_id");
        return json_encode($dpto, JSON_UNESCAPED_UNICODE);
    }


    // public function local($localidad){
    //     $local = ModalityHasLand::join('lands', 'modality_has_lands.land_id', '=', 'lands.id')
    //     ->where('modality_id', $dptoid)->get()->sortBy("name")->pluck("name","land_id");
    //     return json_encode($dpto, JSON_UNESCAPED_UNICODE);
    // }

    public function localedit($dptoid)
    {
        //$tipo = Land_project::where('land_id',$dptoid)->first();
        //dd($tipo);
        $dpto = Departamento::join('LOCALIDA', 'BAMDPT.DptoId', '=', 'LOCALIDA.CiuId')
            ->where('LOCALIDA.CiuId', $dptoid)->get()->sortBy("CiuNom")->pluck("CiuNom", "LOCALIDA.CiuId");
        return json_encode($dpto, JSON_UNESCAPED_UNICODE);
    }

    // public function local($dptoid){
    //     $dpto = Distrito::where('CiuDptoID', $dptoid)->get()->sortBy("CiuNom")->pluck("CiuNom","CiuId");
    //     return json_encode($dpto , JSON_UNESCAPED_UNICODE);
    // }


}
