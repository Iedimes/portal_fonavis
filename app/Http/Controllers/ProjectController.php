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
use App\Models\Assignment;
use App\Models\Typology;
use App\Models\ProjectHasPostulantes;
use App\Models\Land_project;
use App\Models\ModalityHasLand;
use App\Models\Project_tipologies;
use App\Models\ProjectStatus;
use App\Models\User;
use PDF;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Http\Requests\StoreProject;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;

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
        $title="Lista de Proyectos";

        $id = Auth::user()->id;
        $currentuser = User::find($id);

        $projects = Project::where('sat_id', trim($currentuser->sat_ruc))
        ->where('action','=',null)
        ->get();

        //return $projects;
        //Mapper::map(-24.3697635, -56.5912129, ['zoom' => 6, 'type' => 'ROADMAP']);
        return view('projects.index',compact('projects','title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $title="Crear Proyecto";
        $tierra = Land::all();
        $modalidad = Modality::all();
        //$dep = [18, 21, 999];
        $loc = [0, 900];
        //$departamentos = Departamento::all();
        // $departamentos = Departamento::whereNotIn('DptoId', $dep)
        //                  ->orderBy('DptoNom', 'asc')->get();
        $departamentos = Departamento::where('DptoId','<',18)
        ->orderBy('DptoNom', 'asc')->get();
        // $localidad = Distrito::all();

        $localidad = Distrito::whereNotIn('CiuId', $loc)
                     ->orderBy('CiuNom', 'asc')->get();

        $tipologias = Typology::all();
        $id = Auth::user()->id;
        $user = User::find($id);

        //return $user->sat_ruc;
        //return $user->getSat->NucNomSat;
        return view('projects.create',compact('title','tierra','departamentos', 'localidad', 'modalidad','tipologias','user'));
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



    public function checkdocuments($id,$project_id,$sheets)
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




        return "controlador laravel con id: ".$id." y proyecto: ".$project_id;
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
    public function show($id)
    {
        $project=Project::find($id);
        $postulantes = ProjectHasPostulantes::where('project_id',$id)->get();
        //return $postulantes;
        $title="Resumen Proyecto ".$project->name;

        $tipoproy = Land_project::where('land_id',$project->land_id)->first();
        //dd($tipoproy);
        //$documentos = Documents::where('project_id',$id)->get();

        $docproyecto = Assignment::where('project_type_id',$tipoproy->project_type_id)
        //->whereNotIn('document_id', $documentos->pluck('document_id'))
        ->where('category_id',1)
        //->where('stage_id',1)
        ->get();
        $claves = $docproyecto->pluck('document_id');
        $history = ProjectStatus::where('project_id',$project['id'])
                    ->orderBy('created_at')
                    ->get();
        //return $history;
        //return $docproyecto->pluck('document_id')->toArray();
        //dd($docproyecto);
        //$docproyecto = $docproyecto->whereNotIn('document_id', $documentos->pluck('document_id'));
        return view('projects.show',compact('title','project','docproyecto','tipoproy','claves','history','postulantes'));
    }

    public function generatePDF($id)
    {
        $project=Project::find($id);
        //$postulantes = ProjectHasPostulantes::where('project_id',$id)->get();
        $tipoproy = Land_project::where('land_id',$project->land_id)->first();
        $docproyecto = Assignment::where('project_type_id',$tipoproy->project_type_id)
        ->where('category_id',1)
        ->get();
        $codigoQr = QrCode::size(150)->generate(env('APP_URL') . '/' . $project->certificate_pin);
        $data = ['title' => 'Welcome to HDTuto.com',
                'project' => $project,
                'documents' => $docproyecto,
                'valor' => $codigoQr,
                ];
        //$codigoQr = QrCode::size(150)->generate(env('APP_URL') . '/' . $project->certificate_pin);
        $pdf = PDF::loadView('myPDF', $data);

        return $pdf->download('FORMULARIO-INGRESO-'.$project->name.'.pdf');
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

        $title="Editar Proyecto";
        $tierra = Land::all();
        $modalidad = Modality::all();
        $dep = [2, 4, 5, 8, 10, 16, 22];
        $loc = [0, 900];
        $departamentos = Departamento::where('DptoId','<',18)
                         ->orderBy('DptoNom', 'asc')->get();

        $localidad = Distrito::whereNotIn('CiuId', $loc)
                    ->orderBy('CiuNom', 'asc')->get();

        $project=Project::find($id);
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
        return view('projects.edit',compact('title','tierra','typology','lands','departamentos','modalidad','project','tipologias','user', 'localidad'));
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

    // public function upload(Request $request)
    // {
    //     //return $title = $request->title;
    //      return $request;
    //     // return "Upload";
    // 	/*$this->validate($request, [
    // 		//'title' => 'required',
    //         'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    //     ]);*/


    //     $input['file_path'] = time().'.'.$request->image->getClientOriginalExtension();
    //     $request->image->move(public_path('images/'.$request->project_id.'/project/general'), $input['file_path']);

    //     return $title = Document::find($request->title);
    //     //return $title->name;
    //     $input['per_page'] = $title->per_page;
    //     $input['page'] = $request->page;
    //     $input['orderBy'] = $request->page;
    //     $input['orderDirection'] = $request->orderDirection;
    //     Documents::create($input);

    //     //return $input;

    // 	return back()
    //         ->with('success', 'Se ha agregado un Archivo!');
    // }

    public function upload(Request $request)
    {

      // Validación
      $this->validate($request, [
        'archivo' => 'required'
      ]);

      // Obtener ids
      $project_id = $request->project_id;
      $document_id = $request->document_id;

      // Ruta de carpetas
      $folder = "uploads/$project_id/$document_id";

      // Validar documento existente
      $exists = Documents::where('document_id', $document_id)->first();

      if($exists){
        // return back()->withErrors('Documento ya existe')
        //                 ->with('document_id', $request->document_id);
        //dd($project_id);
            return redirect("/projects/$project_id")->withErrors('Documento ya existe');


      }

      // Obtener archivo
      $file = $request->file('archivo');

      // Generar nombre archivo
      $filename = time().rand().'.'.$file->getClientOriginalExtension();

      // Guardar archivo
      try {

        if(!Storage::exists($folder)) {
          Storage::makeDirectory($folder);
        }

        $file->storeAs($folder, $filename);

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

      return redirect("/projects/{$request->project_id}")->with('message', 'Archivo subido');

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
    try {
        $state = new ProjectStatus();
        $state->project_id = $id;
        $state->stage_id = '1';
        $state->user_id = Auth::user()->id;
        $state->record = 'Proyecto Enviado!';
        $state->save();

        // Enviar correo electrónico
        $project = Project::find($id);
        $sat_id= $project->sat_id;
        $sat_nombre = Sat::where('NucCod', $sat_id)->first();
        $nombre_sat = $sat_nombre->NucNomSat;
        $nombre = $project->name;
        $lider = $project->leader_name;
        $modalidad = Modality::where('id', $project->modalidad_id)->first();
        $modalidad_nombre = $modalidad->name;
        $tipo_terreno = Land::where('id',$project->land_id)->first();
        $terreno = $tipo_terreno->name;
        $departamento = Departamento::where('DptoId', $project->state_id)->first();
        $dto = $departamento->DptoNom;
        $ciudad = Distrito::where('CiuId', $project->city_id)->first();
        $destrito = $ciudad->CiuNom;
        $email = 'proyectos_ingresados@muvh.gov.py';
        $subject = 'PROYECTO INGRESADO';

        Mail::send('admin.project-status.emailDGF', ['nombre' => $nombre, 'lider' => $lider, 'sat' => $nombre_sat, 'modalidad' => $modalidad_nombre, 'terreno' => $terreno,'departamento' => $dto ,'project', 'distrito' => $destrito], function ($message) use ($email, $subject) {
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

    public function distrito($dptoid){
        $dpto = Distrito::where('CiuDptoID', $dptoid)->get()->sortBy("CiuNom")->pluck("CiuNom","CiuId");
        return json_encode($dpto , JSON_UNESCAPED_UNICODE);
    }


    // public function distritosinjson($dptoid){
    //     //$dpto =
    //     return Distrito::where('CiuDptoID', $dptoid)->get()->sortBy("CiuNom")->pluck("CiuNom","CiuId");
    //     //return json_encode($dpto, JSON_FORCE_OBJECT);
    //     //return json_encode($dpto , JSON_UNESCAPED_UNICODE);
    // }

    public function lands($dptoid){
        $dpto = ModalityHasLand::join('lands', 'modality_has_lands.land_id', '=', 'lands.id')
        ->where('modality_id', $dptoid)->get()->sortBy("name")->pluck("name","land_id");
        return json_encode($dpto, JSON_UNESCAPED_UNICODE);
    }

    public function typology($dptoid){
        $tipo = Land_project::where('land_id',$dptoid)->first();
        //dd($tipo);
        $dpto = Project_tipologies::join('typologies', 'project_type_has_typologies.typology_id', '=', 'typologies.id')
        ->where('project_type_id',$tipo->project_type_id)->get()->sortBy("name")->pluck("name","typology_id");
        return json_encode($dpto, JSON_UNESCAPED_UNICODE);
    }

    public function typologyedit($dptoid){
        //$tipo = Land_project::where('land_id',$dptoid)->first();
        //dd($tipo);
        $dpto = Project_tipologies::join('typologies', 'project_type_has_typologies.typology_id', '=', 'typologies.id')
        ->where('typology_id',$dptoid)->get()->sortBy("name")->pluck("name","typology_id");
        return json_encode($dpto, JSON_UNESCAPED_UNICODE);
    }


    // public function local($localidad){
    //     $local = ModalityHasLand::join('lands', 'modality_has_lands.land_id', '=', 'lands.id')
    //     ->where('modality_id', $dptoid)->get()->sortBy("name")->pluck("name","land_id");
    //     return json_encode($dpto, JSON_UNESCAPED_UNICODE);
    // }

    public function localedit($dptoid){
        //$tipo = Land_project::where('land_id',$dptoid)->first();
        //dd($tipo);
        $dpto = Departamento::join('LOCALIDA', 'BAMDPT.DptoId', '=', 'LOCALIDA.CiuId')
        ->where('LOCALIDA.CiuId',$dptoid)->get()->sortBy("CiuNom")->pluck("CiuNom","LOCALIDA.CiuId");
        return json_encode($dpto, JSON_UNESCAPED_UNICODE);
    }

    // public function local($dptoid){
    //     $dpto = Distrito::where('CiuDptoID', $dptoid)->get()->sortBy("CiuNom")->pluck("CiuNom","CiuId");
    //     return json_encode($dpto , JSON_UNESCAPED_UNICODE);
    // }


}
