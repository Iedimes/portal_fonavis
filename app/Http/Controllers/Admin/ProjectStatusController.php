<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProjectStatus\BulkDestroyProjectStatus;
use App\Http\Requests\Admin\ProjectStatus\DestroyProjectStatus;
use App\Http\Requests\Admin\ProjectStatus\IndexProjectStatus;
use App\Http\Requests\Admin\ProjectStatus\StoreProjectStatus;
use App\Http\Requests\Admin\ProjectStatus\StoreProjectStatusE;
use App\Http\Requests\Admin\ProjectStatus\UpdateProjectStatus;
use App\Models\ProjectStatus;
use App\Models\ProjectStatusDeletes;
use App\Models\Project;
use App\Models\User;
use App\Models\Sat;
use App\Models\Distrito;
use App\Models\Departamento;
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
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProjectStatusController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexProjectStatus $request
     * @return array|Factory|View
     */
    public function index(IndexProjectStatus $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(ProjectStatus::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'project_id', 'stage_id', 'user_id', 'record'],

            // set columns to searchIn
            ['id', 'record']
        );

        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.project-status.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.project-status.create');

        return view('admin.project-status.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreProjectStatus $request
     * @return array|RedirectResponse|Redirector
     */

    //  public function store(StoreProjectStatus $request)
    //  {
    //      $sanitized = $request->getSanitized();
    //      $sanitized['stage_id'] = $request->getStageId();

    //      $projecto = Project::where('id', $request->project_id)->get();
    //      $sat = $projecto[0]->sat_id;
    //      $useremail = User::where('sat_ruc', $sat)->get()->first();
    //      $satnombre = Sat::where('NucCod', $sat)->get()->first();
    //      $toEmail = $useremail['email'];
    //      $ciudad = Distrito::where('CiuId', $projecto[0]->city_id)->first();
    //      $distrito = $ciudad->CiuNom;
    //      $departamento = Departamento::where('DptoId', $projecto[0]->state_id)->first();
    //      $dto = $departamento->DptoNom;


    //      if ($sanitized['stage_id'] == 2) {
    //          $subject = 'PROYECTO ' .$projecto[0]->name. ' PRESELECCIONADO';

    //          // Store the ProjectStatus
    //          $projectStatus = ProjectStatus::create($sanitized);

    //          try {
    //              Mail::mailer('mail2')->send('admin.project-status.email', ['proyecto' => $projecto[0]->name ,'id' => $projecto[0]->id,'distrito' => $distrito,'dpto' => $dto], function ($message) use ($toEmail, $subject) {
    //                  $message->to($toEmail);
    //                  $message->subject($subject);
    //                  $message->from('preseleccionfonavis@muvh.gov.py', env('APP_NAME'));
    //              });

    //              return response()->json([
    //                  'redirect' => url('admin/projects/' . $request['project_id'] . '/show')
    //              ]);
    //          } catch (Exception $e) {
    //              // Si se produce un error al enviar el correo electrónico, devolvemos una respuesta JSON con un mensaje de error
    //              return response()->json([
    //                  'error' => 'No se pudo enviar el correo electrónico'
    //              ]);
    //          }
    //      } else {
    //          // Store the ProjectStatus
    //          $projectStatus = ProjectStatus::create($sanitized);

    //          return response()->json([
    //              'redirect' => url('admin/projects/' . $request['project_id'] . '/show')
    //          ]);
    //      }

    //      return response()->json([
    //          'redirect' => url('admin/projects/' . $request['project_id'] . '/show')
    //      ]);
    //  }

    public function store(StoreProjectStatus $request)
    {
        $sanitized = $request->getSanitized();
        $sanitized['stage_id'] = $request->getStageId();

        $projecto = Project::where('id', $request->project_id)->get();
        $sat = $projecto[0]->sat_id;
        //$useremail = User::where('sat_ruc', $sat)->get()->first();
        $useremail = 'osemidei@muvh.gov.py'; //Aqui debe ir el correo de DGJN - Recibe DGJN desde DGFO
        $satnombre = Sat::where('NucCod', $sat)->get()->first();
        $toEmail = $useremail;


        //$ciudad = Distrito::where('CiuId', $projecto[0]->city_id)->first();
        //$distrito = $ciudad->CiuNom;
        //$departamento = Departamento::where('DptoId', $projecto[0]->state_id)->first();
        //$dto = $departamento->DptoNom;


        if ($sanitized['stage_id'] == 2) {
            $subject = 'PROYECTO ' .$projecto[0]->name. ' PARA REVISION PRELIMINAR';

            // Store the ProjectStatus
            $projectStatus = ProjectStatus::create($sanitized);

            try {
                Mail::mailer('mail2')->send('admin.project-status.emailDGJN', ['proyecto' => $projecto[0]->name ,'id' => $projecto[0]->id,'sat' => $sat,'satnombre' => $satnombre], function ($message) use ($toEmail, $subject) {
                    $message->to($toEmail);
                    $message->subject($subject);
                    $message->from('preseleccionfonavis@muvh.gov.py', env('APP_NAME'));
                });

                return response()->json([
                    'redirect' => url('admin/projects/' . $request['project_id'] . '/show')
                ]);
            } catch (Exception $e) {
                // Si se produce un error al enviar el correo electrónico, devolvemos una respuesta JSON con un mensaje de error
                //dd($e->getMessage());
                return response()->json([
                    'error' => 'No se pudo enviar el correo electrónico'
                ]);
            }
        }elseif ($sanitized['stage_id'] == 3) {

            $projecto = Project::where('id', $request->project_id)->get();
            $sat = $projecto[0]->sat_id;

            //return "Estamos en estado 3, aqui vamos a devolver el correo a Fonavis";
            $useremail1 = 'preseleccionfonavis@muvh.gov.py'; //Aqui debe ir el correo de DGFO - Recibe de DNJN
            $toEmail = $useremail1;
            $subject = 'INFORME DGJN '.$projecto[0]->name;

            // Store the ProjectStatus
            $projectStatus = ProjectStatus::create($sanitized);

            try {
                Mail::mailer('mail3')->send('admin.project-status.emailDGJNAFONAVIS', ['proyecto' => $projecto[0]->name ,'id' => $projecto[0]->id,'sat' => $sat,'satnombre' => $satnombre], function ($message) use ($toEmail, $subject) {
                    $message->to($toEmail);
                    $message->subject($subject);
                    $message->from('osemidei@muvh.gov.py', env('APP_NAME'));
                });

                return response()->json([
                    'redirect' => url('admin/projects/' . $request['project_id'] . '/showDGJN')
                ]);
            } catch (Exception $e) {
                // Si se produce un error al enviar el correo electrónico, devolvemos una respuesta JSON con un mensaje de error
                //dd($e->getMessage());
                return response()->json([
                    'error' => 'No se pudo enviar el correo electrónico'
                ]);
            }
        }elseif ($sanitized['stage_id'] == 4) { //Estado ARCHIVADO DGJN
            $projecto = Project::where('id', $request->project_id)->get();
            $sat = $projecto[0]->sat_id;
            $useremail = User::where('sat_ruc', $sat)->get()->first();
            $satnombre = Sat::where('NucCod', $sat)->get()->first();

            // Crear un array para almacenar las direcciones de correo electrónico
            $toEmails = [];

            if ($useremail) {
                $toEmails[] = $useremail->email; // se recupera de BD el correo SAT vinculado al proyecto
            }

            // Agregar otras direcciones de correo duro
            $toEmails[] = 'preseleccionfonavis@muvh.gov.py'; // correo FONAVIS
            $toEmails[] = 'nmorel@muvh.gov.py'; // correo FONAVIS - DGSO DESPUES HAY QUE CAMBIAR POR EL QUE CORRESPONDE

            $subject = 'INFORME DGJN EN ARCHIVO '.$projecto[0]->name;

            // Store the ProjectStatus
            $projectStatus = ProjectStatus::create($sanitized);

            try {
                Mail::mailer('mail3')->send('admin.project-status.emailDGJNAFONAVISARCHIVADO', ['proyecto' => $projecto[0]->name ,'id' => $projecto[0]->id,'sat' => $sat,'satnombre' => $satnombre], function ($message) use ($toEmails, $subject) {
                    $message->to($toEmails);
                    $message->subject($subject);
                    $message->from('osemidei@muvh.gov.py', env('APP_NAME'));
                });

                return response()->json([
                    'redirect' => url('admin/projects/' . $request['project_id'] . '/showDGJN')
                ]);
            } catch (Exception $e) {
                // Si se produce un error al enviar el correo electrónico, devolvemos una respuesta JSON con un mensaje de error
                //dd($e->getMessage());
                return response()->json([
                    'error' => 'No se pudo enviar el correo electrónico'
                ]);
            }
        }elseif ($sanitized['stage_id'] == 6) { //Estado RECHAZADO DGJN
            //return "Estado RECHAZADO DGJN";
            $projecto = Project::where('id', $request->project_id)->get();
            $sat = $projecto[0]->sat_id;

            //return "Estamos en estado 6, aqui vamos a devolver el correo a Fonavis";
            $useremail1 = 'preseleccionfonavis@muvh.gov.py'; //Aqui debe ir el correo de DGFO - Recibe de DNJN
            $toEmail = $useremail1;
            $subject = 'INFORME PROYECTO RECHAZADO POR DGJN '.$projecto[0]->name;

            // Store the ProjectStatus
            $projectStatus = ProjectStatus::create($sanitized);

            try {
                Mail::mailer('mail3')->send('admin.project-status.emailDGJNAFONAVISRECHAZADO', ['proyecto' => $projecto[0]->name ,'id' => $projecto[0]->id,'sat' => $sat,'satnombre' => $satnombre], function ($message) use ($toEmail, $subject) {
                    $message->to($toEmail);
                    $message->subject($subject);
                    $message->from('osemidei@muvh.gov.py', env('APP_NAME')); // Mi correo está como si fuera DGJN
                });

                return response()->json([
                    'redirect' => url('admin/projects/' . $request['project_id'] . '/showDGJN')
                ]);
            } catch (Exception $e) {
                // Si se produce un error al enviar el correo electrónico, devolvemos una respuesta JSON con un mensaje de error
                //dd($e->getMessage());
                return response()->json([
                    'error' => 'No se pudo enviar el correo electrónico'
                ]);
            }
        }elseif ($sanitized['stage_id'] == 7) { //Estado EVALUACION SOCIAL
            //return "Estado EVALUACION SOCIAL";
            $projecto = Project::where('id', $request->project_id)->get();
            $sat = $projecto[0]->sat_id;
            $useremail = User::where('sat_ruc', $sat)->get()->first();
            $satnombre = Sat::where('NucCod', $sat)->get()->first();

            // Crear un array para almacenar las direcciones de correo electrónico
            $toEmails = [];

            if ($useremail) {
                $toEmails[] = $useremail->email; // se recupera de BD el correo SAT vinculado al proyecto
            }

            // Agregar otras direcciones de correo duro
            //$toEmails[] = 'preseleccionfonavis@muvh.gov.py'; // correo FONAVIS
            $toEmails[] = 'nmorel@muvh.gov.py'; // correo FONAVIS - DGSO DESPUES HAY QUE CAMBIAR POR EL QUE CORRESPONDE

            $subject = 'INFORME DGJN EN ARCHIVO '.$projecto[0]->name;

            // Store the ProjectStatus
            $projectStatus = ProjectStatus::create($sanitized);

            try {
                Mail::mailer('mail2')->send('admin.project-status.emailFONAVISDGSOSAT', ['proyecto' => $projecto[0]->name ,'id' => $projecto[0]->id,'sat' => $sat,'satnombre' => $satnombre], function ($message) use ($toEmails, $subject) {
                    $message->to($toEmails);
                    $message->subject($subject);
                    $message->from('preseleccionfonavis@muvh.gov.py', env('APP_NAME'));
                });

                return response()->json([
                    'redirect' => url('admin/projects/' . $request['project_id'] . '/showFONAVIS')
                ]);
            } catch (Exception $e) {
                // Si se produce un error al enviar el correo electrónico, devolvemos una respuesta JSON con un mensaje de error
                //dd($e->getMessage());
                return response()->json([
                    'error' => 'No se pudo enviar el correo electrónico'
                ]);
            }
        }elseif ($sanitized['stage_id'] == 9) { //Estado CON DICTAMEN SOCIAL
            return "Estado CON DICTAMEN SOCIAL";
            $projecto = Project::where('id', $request->project_id)->get();
            $sat = $projecto[0]->sat_id;
            //$useremail = User::where('sat_ruc', $sat)->get()->first();
            $useremail = 'preseleccionfonavis@muvh.gov.py';
            $satnombre = Sat::where('NucCod', $sat)->get()->first();


            $toEmail = $useremail;


            $subject = 'INFORME DGSO CON DICTAMEN SOCIAL '.$projecto[0]->name;

            // Store the ProjectStatus
            $projectStatus = ProjectStatus::create($sanitized);

            try {
                Mail::mailer('mail2')->send('admin.project-status.emailFONAVISDGSOSAT', ['proyecto' => $projecto[0]->name ,'id' => $projecto[0]->id,'sat' => $sat,'satnombre' => $satnombre], function ($message) use ($toEmail, $subject) {
                    $message->to($toEmail);
                    $message->subject($subject);
                    $message->from('preseleccionfonavis@muvh.gov.py', env('APP_NAME'));
                });

                return response()->json([
                    'redirect' => url('admin/projects/' . $request['project_id'] . '/showFONAVIS')
                ]);
            } catch (Exception $e) {
                // Si se produce un error al enviar el correo electrónico, devolvemos una respuesta JSON con un mensaje de error
                //dd($e->getMessage());
                return response()->json([
                    'error' => 'No se pudo enviar el correo electrónico'
                ]);
            }
        }



        else {
            // Store the ProjectStatus
            $projectStatus = ProjectStatus::create($sanitized);

            return response()->json([
                'redirect' => url('admin/projects/' . $request['project_id'] . '/show')
            ]);
        }

        return response()->json([
            'redirect' => url('admin/projects/' . $request['project_id'] . '/show')
        ]);
    }




    /**
     * Display the specified resource.
     *
     * @param ProjectStatus $projectStatus
     * @throws AuthorizationException
     * @return void
     */
    public function show(ProjectStatus $projectStatus)
    {
        $this->authorize('admin.project-status.show', $projectStatus);

        // TODO your code goes here
    }


    public function eliminar(Request $request, $projectId) {

        //return $request;
        //return $request['project_id'];

        $projectStatus = ProjectStatus::where('project_id', $projectId)->first();

        $userId = auth()->id();

        $projectStatusDeletes = new ProjectStatusDeletes;

        // Copiar todos los datos de $projectStatus
        $projectStatusDeletes->fill($projectStatus->toArray());

        // Agregar id de usuario
        $projectStatusDeletes->user_id_deleted = $userId;
        $recordValue = $request->input('record');

        $projectStatusDeletes->record = $recordValue;

        $projectStatusDeletes->save();

        // Borrar registro original
        $projectStatus->delete();

        //return redirect()->route('projects.show', ['project' => $projectStatus->project_id]);
        // return view('admin.project/'.$projectId.'/show');
        //return back();

        return response()->json([
            'redirect' => url('admin/projects/' . $request['project_id'] . '/show')
        ]);

      }

    /**
     * Show the form for editing the specified resource.
     *
     * @param ProjectStatus $projectStatus
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(ProjectStatus $projectStatus)
    {
        $this->authorize('admin.project-status.edit', $projectStatus);


        return view('admin.project-status.edit', [
            'projectStatus' => $projectStatus,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateProjectStatus $request
     * @param ProjectStatus $projectStatus
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateProjectStatus $request, ProjectStatus $projectStatus)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values ProjectStatus
        $projectStatus->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/project-statuses'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/project-statuses');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyProjectStatus $request
     * @param ProjectStatus $projectStatus
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyProjectStatus $request, ProjectStatus $projectStatus)
    {
        $projectStatus->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyProjectStatus $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyProjectStatus $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    ProjectStatus::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
