<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Postulante;
use GuzzleHttp\Client;
use App\Models\ProjectHasPostulantes;
use App\Models\Document;
// use App\Models\PostulantesDocuments;
use App\Models\Assignment;
use App\Models\Land_project;
use App\Models\Discapacidad;
use App\Models\Parentesco;
use App\Models\SIG005;
use App\Models\SIG006;
use App\Models\SHMCER;
use App\Models\PRMCLI;
use App\Models\IVMSOL;
use App\Models\ProjectStatus;
use App\Models\PostulanteHasDiscapacidad;
use App\Models\PostulanteHasBeneficiary;
use App\Http\Requests\StorePostulante;
use PDF;

class PostulantesController extends Controller
{
    //

    public function __construct()
    {
        $this->middleware('auth');
        $this->photos_path = public_path('/images');

    }

    public function index($id)
    {
        $title="Lista de Postulantes";
        $project = Project::find($id);
        $postulantes = ProjectHasPostulantes::where('project_id',$id)->get();

        //return $postulantes;
        //Mapper::map(-24.3697635, -56.5912129, ['zoom' => 6, 'type' => 'ROADMAP']);
        return view('postulantes.index',compact('project','title','postulantes'));
        //return "hola";
    }

    public function create(Request $request, $id){

        if ($request->input('cedula')) {

            //$expedientes = SIG005::where('NroExpPer',$request->input('cedula'))->where('TexCod',118)->get();
            $expedientes = SIG005::where('NroExpPer', $request->input('cedula'))
                                   ->where('TexCod', 118)
                                   ->orderBy('NroExp', 'desc')
                                   ->first();


            if(!empty($expedientes) ){

                   //return "Expediente existe";
                   $nroExp = $expedientes->NroExp;
                   // $archivo = SIG006::where('NroExp',$nroExp)->where('DEExpEst', 'A')->get();
                   //return $archivo = SIG006::where('NroExp', $nroExp)->where('DEExpEst', 'A')->exists();
                   $archivo = SIG006::where('NroExp', $nroExp)
                   ->whereIn('DEExpEst', ['C','H'])
                   //->where('DEExpEst', 'A')
                   //->OrWhere('DEExpEst', 'C')
                   ->first();
                   if (!empty($archivo)){
                        //return "Existe detalle que cumple con los requisitos para permitir que se postule";

                       //return redirect()->back()->with('status', 'El expediente tiene estado A!');
                       //$certificados = SHMCER::where('CerPosCod',$request->input('cedula'))->get();
                       $certificados = SHMCER::where('CerPosCod',$request->input('cedula'))
                       ->where('CerEst', '!=', 2)
                       ->where('CerEst', '!=', 7)
                       ->where('CerEst', '!=', 8)
                       ->where('CerEst', '!=', 12)
                       ->get();
                       $certificadosconyuge = SHMCER::where('CerCoCI',$request->input('cedula'))->get();
                       $existe = Postulante::where('cedula',$request->input('cedula'))->get();
                       $cartera = PRMCLI::where('PerCod',$request->input('cedula'))
                       ->where('PylCod','!=' ,'P.F.')
                       //->where('PerCod','!=' ,1211361)
                       ->get();
                       $solicitantes = IVMSOL::where('SolPerCge',$request->input('cedula'))->first();

                       $todos = IVMSOL::where('SolPerCod',$request->input('cedula'))
                        ->where('SolEtapa','B')
                        ->first();
                        if ($todos) {
                            return redirect()->back()->with('status', 'Ya es Beneficiario Final!');
                        }

                       if($existe->count() >= 1){
                           //Session::flash('error', 'Ya existe el postulante!');
                           return redirect()->back()->with('status', 'Ya existe el postulante!');
                       }

                    //    if ($expedientes->count() >= 1) {
                    //        return redirect()->back()->with('status', 'Ya existe expediente de FICHA DE PRE-INSCRIPCION FONAVIS-SVS! - sale por aca 1');
                    //    }

                       if ($certificados->count() >= 1) {
                           return redirect()->back()->with('status', 'Ya cuenta con certificado de Subsidio como Titular!');
                       }

                       if ($certificadosconyuge->count() >= 1) {
                           return redirect()->back()->with('status', 'Ya cuenta con certificado de Subsidio como Conyuge!');
                       }

                       if ($cartera->count() >= 1) {
                           return redirect()->back()->with('status', 'Ya cuenta con Beneficios en la Institución!');
                       }

                       if ($solicitantes) {
                           //dd(trim($solicitantes->SolPerCod));
                           $carterasol = PRMCLI::where('PerCod',trim($solicitantes->SolPerCod))
                           ->where('PylCod','!=' ,'P.F.')
                        //    ->where('PerCod','!=' ,1211361)
                           ->get();
                           if ($carterasol->count() >= 1) {
                               return redirect()->back()->with('status', 'Ya cuenta con Beneficios en la Institución como Conyuge!');
                           }

                       }

                       try {

                       $headers = [
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json'
                    ];

                    $GetOrder = [
                        'username' => 'senavitatconsultas',
                        'password' => 'S3n4vitat'
                    ];
                    $client = new client();
                    $res = $client->post('http://192.168.195.1:8080/mbohape-core/sii/security', [
                        'headers' => $headers,
                        'json' => $GetOrder,
                        'decode_content' => false
                    ]);
                    //var_dump((string) $res->getBody());
                    $contents = $res->getBody()->getContents();
                    $book = json_decode($contents);
                    //echo $book->token;
                    if($book->success == true){
                        //obtener la cedula
                        $headerscedula = [
                            'Authorization' => 'Bearer '.$book->token,
                            'Accept' => 'application/json',
                            'decode_content' => false
                        ];
                        $cedula = $client->get('http://192.168.195.1:8080/frontend-identificaciones/api/persona/obtenerPersonaPorCedula/'.$request->input('cedula'), [
                            'headers' => $headerscedula,
                        ]);
                        $datos=$cedula->getBody()->getContents();
                        $datospersona = json_decode($datos);
                        if(isset($datospersona->obtenerPersonaPorNroCedulaResponse->return->error)){
                            //Flash::error($datospersona->obtenerPersonaPorNroCedulaResponse->return->error);
                            //Session::flash('error', $datospersona->obtenerPersonaPorNroCedulaResponse->return->error);
                            return redirect()->back()->with('status', $datospersona->obtenerPersonaPorNroCedulaResponse->return->error);
                        }else{
                            $nombre = $datospersona->obtenerPersonaPorNroCedulaResponse->return->nombres;
                            $apellido = $datospersona->obtenerPersonaPorNroCedulaResponse->return->apellido;
                            $cedula = $datospersona->obtenerPersonaPorNroCedulaResponse->return->cedula;
                            $sexo = $datospersona->obtenerPersonaPorNroCedulaResponse->return->sexo;
                            $fecha = date('Y-m-d H:i:s.v', strtotime($datospersona->obtenerPersonaPorNroCedulaResponse->return->fechNacim));
                            $nac = $datospersona->obtenerPersonaPorNroCedulaResponse->return->nacionalidadBean;
                            $est = $datospersona->obtenerPersonaPorNroCedulaResponse->return->estadoCivil;
                            //$prof = $datospersona->obtenerPersonaPorNroCedulaResponse->return->profesionBean;
                            $nroexp = $cedula;
                            $title="Agregar Postulante";
                            $project_id = Project::find($id);
                            //$parentesco = Parentesco::all();
                            $discapacdad = Discapacidad::all();
                                //var_dump($datospersona->obtenerPersonaPorNroCedulaResponse);
                            return view('postulantes.create',compact('nroexp','cedula','nombre','apellido','fecha','sexo',
                            'nac','est','title','project_id','discapacdad'/*,'escolaridad','discapacidad','enfermedad','entidades'*/));
                        }

                        //$nombre = $datos->nombres;
                        //echo $cedula->getBody()->getContents();
                    }else{
                        //Flash::success($book->message);
                        return redirect()->back();
                    }

                        } catch (\Exception $e) {
                            // return redirect()->back()->with('status', 'Error al conectar con la API: ' . $e->getMessage());
                            return redirect()->back()->with('status', 'Hubo un problema al conectarse con el servicio de Identificaciones. Por favor, inténtelo nuevamente más tarde.');

                        }

                    }else{
                    //return "Expediente No existe en SIG006 por que esta vacio o no cumple con las condiciones";
                    // $certificados = SHMCER::where('CerPosCod',$request->input('cedula'))->get();
                    $certificados = SHMCER::where('CerPosCod',$request->input('cedula'))
                                    ->where('CerEst', '!=', 2)
                                    ->where('CerEst', '!=', 7)
                                    ->where('CerEst', '!=', 8)
                                    ->where('CerEst', '!=', 12)
                                    ->get();
                    $certificadosconyuge = SHMCER::where('CerCoCI',$request->input('cedula'))->get();
                    $existe = Postulante::where('cedula',$request->input('cedula'))->get();
                    $cartera = PRMCLI::where('PerCod',$request->input('cedula'))
                    ->where('PylCod','!=' ,'P.F.')
                    // ->where('PerCod','!=' ,1211361)
                    ->get();
                    $solicitantes = IVMSOL::where('SolPerCge',$request->input('cedula'))->first();

                    if ($expedientes->count() >= 1) {
                        return redirect()->back()->with('status', 'Ya existe expediente de FICHA DE PRE-INSCRIPCION FONAVIS-SVS!!!');
                    }

                    if($existe->count() >= 1){
                        //Session::flash('error', 'Ya existe el postulante!');
                        return redirect()->back()->with('status', 'Ya existe el postulante!');
                    }

                    $todos = IVMSOL::where('SolPerCod',$request->input('cedula'))
                        ->where('SolEtapa','B')
                        ->first();
                        if ($todos) {
                            return redirect()->back()->with('status', 'Ya es Beneficiario Final!');
                        }


                    if ($certificados->count() >= 1) {
                        return redirect()->back()->with('status', 'Ya cuenta con certificado de Subsidio como Titular!');
                    }

                    if ($certificadosconyuge->count() >= 1) {
                        return redirect()->back()->with('status', 'Ya cuenta con certificado de Subsidio como Conyuge!');
                    }

                    if ($cartera->count() >= 1) {
                        return redirect()->back()->with('status', 'Ya cuenta con Beneficios en la Institución!');
                    }

                    if ($solicitantes) {
                        //dd(trim($solicitantes->SolPerCod));
                        $carterasol = PRMCLI::where('PerCod',trim($solicitantes->SolPerCod))
                        ->where('PylCod','!=' ,'P.F.')
                        // ->where('PerCod','!=' ,1211361)
                        ->get();
                        if ($carterasol->count() >= 1) {
                            return redirect()->back()->with('status', 'Ya cuenta con Beneficios en la Institución como Conyuge!');
                        }

                    }
            }
            }else{ // termina Expedientes

                //return "Si el expediente no existe, se deben controlar todas las otras tablas";


            //    $certificados = SHMCER::where('CerPosCod',$request->input('cedula'))->get();
                $certificados = SHMCER::where('CerPosCod',$request->input('cedula'))
                                        ->where('CerEst', '!=', 2)
                                        ->where('CerEst', '!=', 7)
                                        ->where('CerEst', '!=', 8)
                                        ->where('CerEst', '!=', 12)
                                        ->get();
               $certificadosconyuge = SHMCER::where('CerCoCI',$request->input('cedula'))->get();
               $existe = Postulante::where('cedula',$request->input('cedula'))->get();
               $cartera = PRMCLI::where('PerCod',$request->input('cedula'))
               ->where('PylCod','!=' ,'P.F.')
            //    ->where('PerCod','!=' ,1211361)
               ->get();
               $solicitantes = IVMSOL::where('SolPerCge',$request->input('cedula'))->first();
               $todos = IVMSOL::where('SolPerCod',$request->input('cedula'))
               ->where('SolEtapa','B')
               ->first();


               if($existe->count() >= 1){
                   //Session::flash('error', 'Ya existe el postulante!');
                   return redirect()->back()->with('status', 'Ya existe el postulante!');
               }

               if ($certificados->count() >= 1) {
                   return redirect()->back()->with('status', 'Ya cuenta con certificado de Subsidio como Titular!');
               }

               if ($certificadosconyuge->count() >= 1) {
                   return redirect()->back()->with('status', 'Ya cuenta con certificado de Subsidio como Conyuge!');
               }

               if ($cartera->count() >= 1) {
                   return redirect()->back()->with('status', 'Ya cuenta con Beneficios en la Institución!');
               }

               if ($solicitantes) {
                   //dd(trim($solicitantes->SolPerCod));
                   $carterasol = PRMCLI::where('PerCod',trim($solicitantes->SolPerCod))
                   ->where('PylCod','!=' ,'P.F.')
                //    ->where('PerCod','!=' ,1211361)
                   ->get();
                   if ($carterasol->count() >= 1) {
                       return redirect()->back()->with('status', 'Ya cuenta con Beneficios en la Institución como Conyuge!');
                   }

               }


               if ($todos) {
                return redirect()->back()->with('status', 'Ya es Beneficiario Final!');
            }

            }
            // return "sale por el ya existe, pero se le debe dejar continuar";
            // return "sale por el no existe";

            try{

            $headers = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ];

            $GetOrder = [
                'username' => 'senavitatconsultas',
                'password' => 'S3n4vitat'
            ];
            $client = new client();
            $res = $client->post('http://192.168.195.1:8080/mbohape-core/sii/security', [
                'headers' => $headers,
                'json' => $GetOrder,
                'decode_content' => false
            ]);
            //var_dump((string) $res->getBody());
            $contents = $res->getBody()->getContents();
            $book = json_decode($contents);
            //echo $book->token;
            if($book->success == true){
                //obtener la cedula
                $headerscedula = [
                    'Authorization' => 'Bearer '.$book->token,
                    'Accept' => 'application/json',
                    'decode_content' => false
                ];
                $cedula = $client->get('http://192.168.195.1:8080/frontend-identificaciones/api/persona/obtenerPersonaPorCedula/'.$request->input('cedula'), [
                    'headers' => $headerscedula,
                ]);
                $datos=$cedula->getBody()->getContents();
                $datospersona = json_decode($datos);
                if(isset($datospersona->obtenerPersonaPorNroCedulaResponse->return->error)){
                    //Flash::error($datospersona->obtenerPersonaPorNroCedulaResponse->return->error);
                    //Session::flash('error', $datospersona->obtenerPersonaPorNroCedulaResponse->return->error);
                    return redirect()->back()->with('status', $datospersona->obtenerPersonaPorNroCedulaResponse->return->error);
                }else{
                    $nombre = $datospersona->obtenerPersonaPorNroCedulaResponse->return->nombres;
                    $apellido = $datospersona->obtenerPersonaPorNroCedulaResponse->return->apellido;
                    $cedula = $datospersona->obtenerPersonaPorNroCedulaResponse->return->cedula;
                    $sexo = $datospersona->obtenerPersonaPorNroCedulaResponse->return->sexo;
                    $fecha = date('Y-m-d H:i:s.v', strtotime($datospersona->obtenerPersonaPorNroCedulaResponse->return->fechNacim));
                    $nac = $datospersona->obtenerPersonaPorNroCedulaResponse->return->nacionalidadBean;
                    $est = $datospersona->obtenerPersonaPorNroCedulaResponse->return->estadoCivil;
                    //$prof = $datospersona->obtenerPersonaPorNroCedulaResponse->return->profesionBean;
                    $nroexp = $cedula;
                    $title="Agregar Postulante";
                    $project_id = Project::find($id);
                    //$parentesco = Parentesco::all();
                    $discapacdad = Discapacidad::all();
                        //var_dump($datospersona->obtenerPersonaPorNroCedulaResponse);
                    return view('postulantes.create',compact('nroexp','cedula','nombre','apellido','fecha','sexo',
                    'nac','est','title','project_id','discapacdad'/*,'escolaridad','discapacidad','enfermedad','entidades'*/));
                }

                //$nombre = $datos->nombres;
                //echo $cedula->getBody()->getContents();
            }else{
                //Flash::success($book->message);
                return redirect()->back();
            }

            } catch (\Exception $e) {
                // return redirect()->back()->with('status', 'Error al conectar con la API: ' . $e->getMessage());
                return redirect()->back()->with('status', 'Hubo un problema al conectarse con el servicio de Identificaciones. Por favor, inténtelo nuevamente más tarde.');
            }
        }else{

            return redirect()->back()->with('error', 'Ingrese Cédula');
        }

        $title="Agregar Postulante";
        return view('postulantes.create',compact('title'));
    }



    public function createmiembro(Request $request, $id, $x){

        //return $request;

        //return $id;
        //return $x;

        $proyectoEstado = ProjectStatus::where('project_id', $id)->latest()->first();

        if ($proyectoEstado) {
            $ultimoEstado = $proyectoEstado->stage_id;
        } else {
            $ultimoEstado = null; // O cualquier otro valor que desees asignar para indicar que está vacío
        }

        //return "Crear Miembro";



    if ($request->input('cedula')) {

        //$expedientes = SIG005::where('NroExpPer',$request->input('cedula'))->where('TexCod',118)->get();
        $expedientes = SIG005::where('NroExpPer', $request->input('cedula'))
                               ->where('TexCod', 118)
                               ->orderBy('NroExp', 'desc')
                               ->first();


        if(!empty($expedientes) ){

               //return "Expediente existe";
               $nroExp = $expedientes->NroExp;
               // $archivo = SIG006::where('NroExp',$nroExp)->where('DEExpEst', 'A')->get();
               //return $archivo = SIG006::where('NroExp', $nroExp)->where('DEExpEst', 'A')->exists();
               $archivo = SIG006::where('NroExp', $nroExp)
               ->whereIn('DEExpEst', ['C', 'H'])
               //->where('DEExpEst', 'A')
               //->OrWhere('DEExpEst', 'C')
               ->first();
               if (!empty($archivo)){
                    //return "Existe detalle que cumple con los requisitos para permitir que se postule";

                   //return redirect()->back()->with('status', 'El expediente tiene estado A!');
                   //$certificados = SHMCER::where('CerPosCod',$request->input('cedula'))->get();
                   $certificados = SHMCER::where('CerPosCod',$request->input('cedula'))
                   ->where('CerEst', '!=', 2)
                   ->where('CerEst', '!=', 7)
                   ->where('CerEst', '!=', 8)
                   ->where('CerEst', '!=', 12)
                   ->get();
                   $certificadosconyuge = SHMCER::where('CerCoCI',$request->input('cedula'))->get();
                   $existe = Postulante::where('cedula',$request->input('cedula'))->get();
                   $cartera = PRMCLI::where('PerCod',$request->input('cedula'))
                   ->where('PylCod','!=' ,'P.F.')
                //    ->where('PerCod','!=' ,1211361)
                   ->get();
                   $solicitantes = IVMSOL::where('SolPerCge',$request->input('cedula'))->first();

                   $todos = IVMSOL::where('SolPerCod',$request->input('cedula'))
                    ->where('SolEtapa','B')
                    ->first();
                    if ($todos) {
                        return redirect()->back()->with('status', 'Ya es Beneficiario Final!');
                    }

                   if($existe->count() >= 1){
                       //Session::flash('error', 'Ya existe el postulante!');
                       return redirect()->back()->with('status', 'Ya existe el postulante!');
                   }

                //    if ($expedientes->count() >= 1) {
                //        return redirect()->back()->with('status', 'Ya existe expediente de FICHA DE PRE-INSCRIPCION FONAVIS-SVS! - sale por aca 1');
                //    }

                   if ($certificados->count() >= 1) {
                       return redirect()->back()->with('status', 'Ya cuenta con certificado de Subsidio como Titular!');
                   }

                   if ($certificadosconyuge->count() >= 1) {
                       return redirect()->back()->with('status', 'Ya cuenta con certificado de Subsidio como Conyuge!');
                   }

                   if ($cartera->count() >= 1) {
                       return redirect()->back()->with('status', 'Ya cuenta con Beneficios en la Institución!');
                   }

                   if ($solicitantes) {
                       //dd(trim($solicitantes->SolPerCod));
                       $carterasol = PRMCLI::where('PerCod',trim($solicitantes->SolPerCod))
                       ->where('PylCod','!=' ,'P.F.')
                    //    ->where('PerCod','!=' ,1211361)
                       ->get();
                       if ($carterasol->count() >= 1) {
                           return redirect()->back()->with('status', 'Ya cuenta con Beneficios en la Institución como Conyuge!');
                       }

                   }

                   //return "sale por aca";

                   $headers = [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ];

                $GetOrder = [
                    'username' => 'senavitatconsultas',
                    'password' => 'S3n4vitat'
                ];
                $client = new client();
                $res = $client->post('http://192.168.195.1:8080/mbohape-core/sii/security', [
                    'headers' => $headers,
                    'json' => $GetOrder,
                    'decode_content' => false
                ]);
                //var_dump((string) $res->getBody());
                $contents = $res->getBody()->getContents();
                $book = json_decode($contents);
                //echo $book->token;
                if($book->success == true){
                    //obtener la cedula
                    $headerscedula = [
                        'Authorization' => 'Bearer '.$book->token,
                        'Accept' => 'application/json',
                        'decode_content' => false
                    ];
                    $cedula = $client->get('http://192.168.195.1:8080/frontend-identificaciones/api/persona/obtenerPersonaPorCedula/'.$request->input('cedula'), [
                        'headers' => $headerscedula,
                    ]);
                    $datos=$cedula->getBody()->getContents();
                    $datospersona = json_decode($datos);
                    if(isset($datospersona->obtenerPersonaPorNroCedulaResponse->return->error)){
                        //Flash::error($datospersona->obtenerPersonaPorNroCedulaResponse->return->error);
                        //Session::flash('error', $datospersona->obtenerPersonaPorNroCedulaResponse->return->error);
                        return redirect()->back()->with('status', $datospersona->obtenerPersonaPorNroCedulaResponse->return->error);
                    }else{
                        $nombre = $datospersona->obtenerPersonaPorNroCedulaResponse->return->nombres;
                        $apellido = $datospersona->obtenerPersonaPorNroCedulaResponse->return->apellido;
                        $cedula = $datospersona->obtenerPersonaPorNroCedulaResponse->return->cedula;
                        $sexo = $datospersona->obtenerPersonaPorNroCedulaResponse->return->sexo;
                        $fecha = date('Y-m-d H:i:s.v', strtotime($datospersona->obtenerPersonaPorNroCedulaResponse->return->fechNacim));
                        $nac = $datospersona->obtenerPersonaPorNroCedulaResponse->return->nacionalidadBean;
                        return $est = $datospersona->obtenerPersonaPorNroCedulaResponse->return->estadoCivil;
                        //$prof = $datospersona->obtenerPersonaPorNroCedulaResponse->return->profesionBean;
                        $nroexp = $cedula;
                        $title="Agregar Miembro Familiar";
                        $project_id = Project::find($id);
                        $par = [1, 8];
                        if ($ultimoEstado==7 || $ultimoEstado==NULL){

                            $parentesco = Parentesco::whereIn('id', $par)
                                                ->orderBy('name', 'asc')->get();
                            $discapacdad = Discapacidad::all();
                            $idpostulante = $x;

                        }else{

                            $parentesco = Parentesco::whereIn('id', $par)
                            ->orderBy('name', 'asc')->get();
                        $discapacdad = Discapacidad::all();
                        $idpostulante = $x;
                            //var_dump($datospersona->obtenerPersonaPorNroCedulaResponse);
                        }

                        return view('postulantes.ficha.createmiembro',compact('nroexp','cedula','nombre','apellido','fecha','sexo',
                        'nac','est','title','project_id','discapacdad','parentesco' , 'idpostulante'/*,'escolaridad','discapacidad','enfermedad','entidades'*/));
                    }

                    //$nombre = $datos->nombres;
                    //echo $cedula->getBody()->getContents();
                }else{
                    //Flash::success($book->message);
                    return redirect()->back();
                }

                }else{
                //return "Expediente No existe en SIG006 por que esta vacio o no cumple con las condiciones";
                // $certificados = SHMCER::where('CerPosCod',$request->input('cedula'))->get();
                $certificados = SHMCER::where('CerPosCod',$request->input('cedula'))
                                ->where('CerEst', '!=', 2)
                                ->where('CerEst', '!=', 7)
                                ->where('CerEst', '!=', 8)
                                ->where('CerEst', '!=', 12)
                                ->get();
                $certificadosconyuge = SHMCER::where('CerCoCI',$request->input('cedula'))->get();
                $existe = Postulante::where('cedula',$request->input('cedula'))->get();
                $cartera = PRMCLI::where('PerCod',$request->input('cedula'))
                ->where('PylCod','!=' ,'P.F.')
                // ->where('PerCod','!=' ,1211361)
                ->get();
                $solicitantes = IVMSOL::where('SolPerCge',$request->input('cedula'))->first();

                if ($expedientes->count() >= 1) {
                    return redirect()->back()->with('status', 'Ya existe expediente de FICHA DE PRE-INSCRIPCION FONAVIS-SVS!!!');
                }

                if($existe->count() >= 1){
                    //Session::flash('error', 'Ya existe el postulante!');
                    return redirect()->back()->with('status', 'Ya existe el postulante!');
                }

                $todos = IVMSOL::where('SolPerCod',$request->input('cedula'))
                    ->where('SolEtapa','B')
                    ->first();
                    if ($todos) {
                        return redirect()->back()->with('status', 'Ya es Beneficiario Final!');
                    }


                if ($certificados->count() >= 1) {
                    return redirect()->back()->with('status', 'Ya cuenta con certificado de Subsidio como Titular!');
                }

                if ($certificadosconyuge->count() >= 1) {
                    return redirect()->back()->with('status', 'Ya cuenta con certificado de Subsidio como Conyuge!');
                }

                if ($cartera->count() >= 1) {
                    return redirect()->back()->with('status', 'Ya cuenta con Beneficios en la Institución!');
                }

                if ($solicitantes) {
                    //dd(trim($solicitantes->SolPerCod));
                    $carterasol = PRMCLI::where('PerCod',trim($solicitantes->SolPerCod))
                    ->where('PylCod','!=' ,'P.F.')
                    // ->where('PerCod','!=' ,1211361)
                    ->get();
                    if ($carterasol->count() >= 1) {
                        return redirect()->back()->with('status', 'Ya cuenta con Beneficios en la Institución como Conyuge!');
                    }

                }
        }
        }else{ // termina Expedientes

            //return "Si el expediente no existe, se deben controlar todas las otras tablas";


        //    $certificados = SHMCER::where('CerPosCod',$request->input('cedula'))->get();
            $certificados = SHMCER::where('CerPosCod',$request->input('cedula'))
                                    ->where('CerEst', '!=', 2)
                                    ->where('CerEst', '!=', 7)
                                    ->where('CerEst', '!=', 8)
                                    ->where('CerEst', '!=', 12)
                                    ->get();
           $certificadosconyuge = SHMCER::where('CerCoCI',$request->input('cedula'))->get();
           $existe = Postulante::where('cedula',$request->input('cedula'))->get();
           $cartera = PRMCLI::where('PerCod',$request->input('cedula'))
           ->where('PylCod','!=' ,'P.F.')
        //    ->where('PerCod','!=' ,1211361)
           ->get();
           $solicitantes = IVMSOL::where('SolPerCge',$request->input('cedula'))->first();
           $todos = IVMSOL::where('SolPerCod',$request->input('cedula'))
           ->where('SolEtapa','B')
           ->first();


           if($existe->count() >= 1){
               //Session::flash('error', 'Ya existe el postulante!');
               return redirect()->back()->with('status', 'Ya existe el postulante!');
           }

           if ($certificados->count() >= 1) {
               return redirect()->back()->with('status', 'Ya cuenta con certificado de Subsidio como Titular!');
           }

           if ($certificadosconyuge->count() >= 1) {
               return redirect()->back()->with('status', 'Ya cuenta con certificado de Subsidio como Conyuge!');
           }

           if ($cartera->count() >= 1) {
               return redirect()->back()->with('status', 'Ya cuenta con Beneficios en la Institución!');
           }

           if ($solicitantes) {
               //dd(trim($solicitantes->SolPerCod));
               $carterasol = PRMCLI::where('PerCod',trim($solicitantes->SolPerCod))
               ->where('PylCod','!=' ,'P.F.')
            //    ->where('PerCod','!=' ,1211361)
               ->get();
               if ($carterasol->count() >= 1) {
                   return redirect()->back()->with('status', 'Ya cuenta con Beneficios en la Institución como Conyuge!');
               }

           }


           if ($todos) {
            return redirect()->back()->with('status', 'Ya es Beneficiario Final!');
        }

        }
        // return "sale por el ya existe, pero se le debe dejar continuar";
        // return "sale por el no existe";

        //return "sale por aca2";

        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];

        $GetOrder = [
            'username' => 'senavitatconsultas',
            'password' => 'S3n4vitat'
        ];
        $client = new client();
        $res = $client->post('http://192.168.195.1:8080/mbohape-core/sii/security', [
            'headers' => $headers,
            'json' => $GetOrder,
            'decode_content' => false
        ]);
        //var_dump((string) $res->getBody());
        $contents = $res->getBody()->getContents();
        $book = json_decode($contents);
        //echo $book->token;
        if($book->success == true){
            //obtener la cedula
            $headerscedula = [
                'Authorization' => 'Bearer '.$book->token,
                'Accept' => 'application/json',
                'decode_content' => false
            ];
            $cedula = $client->get('http://192.168.195.1:8080/frontend-identificaciones/api/persona/obtenerPersonaPorCedula/'.$request->input('cedula'), [
                'headers' => $headerscedula,
            ]);
            $datos=$cedula->getBody()->getContents();
            $datospersona = json_decode($datos);
            if(isset($datospersona->obtenerPersonaPorNroCedulaResponse->return->error)){
                //Flash::error($datospersona->obtenerPersonaPorNroCedulaResponse->return->error);
                //Session::flash('error', $datospersona->obtenerPersonaPorNroCedulaResponse->return->error);
                return redirect()->back()->with('status', $datospersona->obtenerPersonaPorNroCedulaResponse->return->error);
            }else{
                $nombre = $datospersona->obtenerPersonaPorNroCedulaResponse->return->nombres;
                $apellido = $datospersona->obtenerPersonaPorNroCedulaResponse->return->apellido;
                $cedula = $datospersona->obtenerPersonaPorNroCedulaResponse->return->cedula;
                $sexo = $datospersona->obtenerPersonaPorNroCedulaResponse->return->sexo;
                $fecha = date('Y-m-d H:i:s.v', strtotime($datospersona->obtenerPersonaPorNroCedulaResponse->return->fechNacim));
                $nac = $datospersona->obtenerPersonaPorNroCedulaResponse->return->nacionalidadBean;
                $est = $datospersona->obtenerPersonaPorNroCedulaResponse->return->estadoCivil;
                //$prof = $datospersona->obtenerPersonaPorNroCedulaResponse->return->profesionBean;
                $nroexp = $cedula;
                $title="Agregar Miembro Familiar";
                $project_id = Project::find($id);
                $proyectoEstado = ProjectStatus::where('project_id', $id)->latest()->first();

                if ($proyectoEstado) {
                    $ultimoEstado = $proyectoEstado->stage_id;
                } else {
                    $ultimoEstado = null; // O cualquier otro valor que desees asignar para indicar que está vacío
                }
                $par = [1, 8];
                if ($ultimoEstado==7 || $ultimoEstado=NULL){

                   // return "Vacio";

                    // $parentesco = Parentesco::whereIn('id', $par)
                    //                       ->orderBy('name', 'asc')->get();
                    $parentesco = Parentesco::all();
                    $discapacdad = Discapacidad::all();
                    $idpostulante = $x;

                }else{

                // $parentesco = Parentesco::all();
                $parentesco = Parentesco::whereIn('id', $par)
                                          ->orderBy('name', 'asc')->get();
                $discapacdad = Discapacidad::all();
                $idpostulante = $x;
                    //var_dump($datospersona->obtenerPersonaPorNroCedulaResponse);
                }

                //return "parentesco:".$parentesco;
                return view('postulantes.ficha.createmiembro',compact('nroexp','cedula','nombre','apellido','fecha','sexo',
                'nac','est','title','project_id','discapacdad','idpostulante','parentesco'/*,'escolaridad','discapacidad','enfermedad','entidades'*/));
            }

            //$nombre = $datos->nombres;
            //echo $cedula->getBody()->getContents();
        }else{
            //Flash::success($book->message);
            return redirect()->back();
        }
    }else{

        return redirect()->back()->with('error', 'Ingrese Cédula');
    }

    $title="Agregar Miembro Familiar";
    return view('postulantes.create',compact('title'));
}


    public function store(StorePostulante $request)
    {


        //return $request;

        $input = $request->except(['_token','project_id','discapacidad_id']);
        $postulante = Postulante ::create($input);

        $proypostulante = new ProjectHasPostulantes();
        $proypostulante->project_id=$request->project_id;
        $proypostulante->postulante_id=$postulante->id;
        $proypostulante->save();



        $postulantediscapacidad = new PostulanteHasDiscapacidad();
        $postulantediscapacidad->discapacidad_id=$request->discapacidad_id;
        $postulantediscapacidad->postulante_id=$postulante->id;
        $postulantediscapacidad->save();
        //ProjectHasPostulantes::

        //return $request->all();
        //Postulante ::create($request->all());
        return redirect('projects/'.$request->project_id.'/postulantes')->with('success', 'Se ha agregado un nuevo Postulante!');
        //return $request;
    }

    public function storeEditPostulante(StorePostulante $request)
    {
        $postulante = Postulante::findOrFail($request->postulante_id);
        $postulante->fill($request->all());
        $postulante->save();

        return redirect('projects/'.$request->project_id.'/postulantes')->with('success', 'Se ha editado correctamente el Postulante!');
    }

    public function storemiembro(Request $request)
    {
        //return $request;
        $input = $request->except(['_token','project_id','discapacidad_id','postulante_id']);
        $postulante = Postulante ::create($input);

        $miembro = new PostulanteHasBeneficiary();
        $miembro->postulante_id=$request->postulante_id;
        $miembro->miembro_id=$postulante->id;
        $miembro->parentesco_id=$request->parentesco_id;
        $miembro->save();

        $postulantediscapacidad = new PostulanteHasDiscapacidad();
        $postulantediscapacidad->discapacidad_id=$request->discapacidad_id;
        $postulantediscapacidad->postulante_id=$postulante->id;
        $postulantediscapacidad->save();
        //ProjectHasPostulantes::

        //return $request->all();
        //Postulante ::create($request->all());
        //return redirect('projects/'.$request->project_id.'/postulantes/'.$request->postulante_id)->with('success', 'Se ha agregado un nuevo Miembro!');
        return redirect('projects/'.$request->project_id.'/postulantes')->with('success', 'Se ha agregado un nuevo Miembro!');
        //return $request;
    }

    public function storemiembroeditar(Request $request){
       // return "Guardar Edicion";
        $postulante = Postulante::findOrFail($request->postulante_id);
        $postulante->fill($request->all());
        $postulante->save();
        return redirect('projects/'.$request->project_id.'/postulantes/'.$request->postulante_id)->with('success', 'Se ha editado correctamente el Miembro!');
    }

    public function show($id,$idpostulante)
    {
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
        return view('postulantes.show',compact('title','project','miembros','postulante'));
    }

    public function edit($id,$idpostulante)
    {
        $title="Editar Postulante";
        $project=Project::find($id);
        $postulante=Postulante::find($idpostulante);
        $nombre = $postulante->first_name;
        $apellido = $postulante->last_name;
        $cedula = $postulante->cedula;
        $sexo = $postulante->gender;
        $project_id = Project::find($id);
        $nac = $postulante->nacionalidad;
        $est = $postulante->marital_status;
        $fecha = $postulante->birthdate;
        $discapacdad = Discapacidad::all();
        $disc = PostulanteHasDiscapacidad::where('postulante_id',$postulante->id)->first();

        return view('postulantes.create',compact('title','project','postulante','apellido','cedula','sexo','project_id',
                                                'nombre','nac','est','fecha','discapacdad','disc'));
    }

    public function editarPostulante($id,$idpostulante)
    {
        $title="Editar Postulante";
        $project=Project::find($id);
        $postulante=Postulante::find($idpostulante);
        $nombre = $postulante->first_name;
        $apellido = $postulante->last_name;
        $cedula = $postulante->cedula;
        $sexo = $postulante->gender;
        $project_id = Project::find($id);
        $nac = $postulante->nacionalidad;
        $est = $postulante->marital_status;
        $fecha = $postulante->birthdate;
        $discapacdad = Discapacidad::all();
        $disc = PostulanteHasDiscapacidad::where('postulante_id',$postulante->id)->first();

        return view('postulantes.edit',compact('title','project','postulante','apellido','cedula','sexo','project_id',
                                                'nombre','nac','est','fecha','discapacdad','disc'));
    }



    public function editmiembro($id,$idpostulante)
    {
        $title="Editar Miembro";
        $project=Project::find($id);
        $postulante=Postulante::find($idpostulante);
        $nombre = $postulante->first_name;
        $apellido = $postulante->last_name;
        $cedula = $postulante->cedula;
        $sexo = $postulante->gender;
        $project_id = Project::find($id);
        $nac = $postulante->nacionalidad;
        $est = $postulante->marital_status;
        $fecha = $postulante->birthdate;
        $discapacdad = Discapacidad::all();
        $disc = PostulanteHasDiscapacidad::where('postulante_id',$postulante->id)->first();
        $estado= ProjectStatus::where('project_id', $id)->first();
        if (empty($estado)){
            //return "Vacio";
            $par = [1,8];
            $parentesco = Parentesco::whereIn('id', $par)->get();
        }else{
            $parentesco = Parentesco::all();
        }

        $parent = PostulanteHasBeneficiary::where('miembro_id',$postulante->id)->first();
        $idpostulante=$parent->postulante_id;
        $idmiembro=$parent->miembro_id;

        return view('postulantes.ficha.editmiembro',compact('title','project','postulante','apellido','cedula','sexo','project_id',
                                                'nombre','nac','est','fecha','discapacdad','disc','parentesco','idpostulante', 'idmiembro'));
    }

    public function update(Request $request)
    {
        //
        //return $request;
        $postulante = Postulante::find($request->input("id"));
        $postulante->localidad = $request->input("localidad");
        $postulante->address = $request->input("address");
        $postulante->cedula = $request->input("cedula");
        $postulante->phone = $request->input("phone");
        $postulante->asentamiento = $request->input("asentamiento");
        $postulante->ingreso = $request->input("ingreso");
        $postulante->mobile = $request->input("mobile");
        $postulante->save();

        $disc = PostulanteHasDiscapacidad::find($request->input("disc_id"));
        $disc->discapacidad_id=$request->discapacidad_id;
        $disc->save();






        return redirect('projects/'.$request->input("project_id").'/postulantes')->with('success', 'El postulante fue actualizado!');
    }

    public function updatemiembro(Request $request)
    {
        //return $request;
        //return $request->parent_id;
        $postulante = Postulante::find($request->parent_id);
        $postulante->localidad = $request->input("localidad");
        $postulante->address = $request->input("address");
        $postulante->cedula = $request->input("cedula");
        $postulante->phone = $request->input("phone");
        $postulante->asentamiento = $request->input("asentamiento");
        $postulante->ingreso = $request->input("ingreso");
        $postulante->mobile = $request->input("mobile");
        $postulante->save();

        $disc = PostulanteHasDiscapacidad::find($request->disc_id);
        $disc->discapacidad_id=$request->discapacidad_id;
        $disc->save();


        $parent = PostulanteHasBeneficiary::where('miembro_id', $request->parent_id)->first();
        $parent->parentesco_id=$request->parentesco_id;
        $parent->save();

        return redirect('projects/'.$request->input("project_id").'/postulantes/'.$request->input("postulante_id"))->with('success', 'El miembro fue actualizado!');
    }

    public function generatePDF($id)
    {
        $project=Project::find($id);
        $postulantes = ProjectHasPostulantes::where('project_id',$id)->get();
        $contar = count($postulantes);
        $pdf = PDF::loadView('postulantesPDF', compact('project','postulantes', 'contar'))->setPaper('a4', 'landscape');

        return $pdf->download('Listadopostulantes.pdf');
    }



    public function upload(Request $request)
    {

        $input['file_path'] = time().'.'.$request->image->getClientOriginalExtension();
        $request->image->move(public_path('images/'.$request->project_id.'/project/general'), $input['file_path']);

        $title = Document::find($request->title);
        //return $title->name;
        $input['title'] = $title->name;
        $input['postulante_id'] = $request->postulante_id;
        $input['document_id'] = $request->title;
        PostulantesDocuments::create($input);

        //return $input;

    	return back()
            ->with('success', 'Se ha agregado un Archivo!');
    }

    public function destroy(Request $request)
    {


        $postulante = ProjectHasPostulantes::where('postulante_id',$request->delete_id)->first();


        if ($postulante->getMembers->count() > 0) {
            // return back()->with('error', 'Debe eliminar todos los miembros antes de eliminar el postulante!');
            return back()->with('status', 'Debe eliminar todos los miembros antes de eliminar el postulante!');
        }else{

        ProjectHasPostulantes::where('postulante_id',$request->delete_id)->delete();
        PostulanteHasDiscapacidad::where('postulante_id',$request->delete_id)->delete();
        Postulante::find($request->delete_id)->delete();

        // return back()->with('error', 'Se ha eliminado el Postulante!');
        return back()->with('status', 'Se ha eliminado el Postulante!');
        }

    }


    public function destroymiembro(Request $request)
    {
        PostulanteHasBeneficiary::where('miembro_id',$request->delete_idmiembro)->delete();
        PostulanteHasDiscapacidad::where('postulante_id',$request->delete_idmiembro)->delete();
        Postulante::find($request->delete_idmiembro)->delete();
        return back()->with('error', 'Se ha eliminado el Miembro!');
    }

    public function destroyfile(Request $request)
    {

        $file = PostulantesDocuments::find($request->delete_id);

        $file_path = $this->photos_path . '/' . $file->project_id . '/project/general/' . $file->file_path;
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        PostulantesDocuments::find($request->delete_id)->delete();
        return back()->with('error', 'Se ha eliminado el archivo!');
    }

}
