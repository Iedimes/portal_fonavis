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
use App\Models\AdminUsersDependency;
use App\Models\AdminUser;
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


    public function store(StoreProjectStatus $request)
    {
        $sanitized = $request->getSanitized();
        $sanitized['stage_id'] = $request->getStageId();
        $dependencia = $sanitized['dependencia'];

        // Variable para controlar envío de correos
        $mailEnabled = env('MAIL_ENABLED', true);

        $projecto = Project::where('id', $request->project_id)->first();
        $sat = $projecto->sat_id;
        $satnombre = Sat::where('NucCod', $sat)->select('NucNomSat')->first();


        if ($sanitized['stage_id'] == 2) {

            $dependenciaDGJN = AdminUsersDependency::where('dependency_id', 2)
                                                ->pluck('admin_user_id');

            $usuarios = AdminUser::whereIn('id', $dependenciaDGJN)->get();

            $userEmails = $usuarios->pluck('email')->toArray();
            $subject = 'PROYECTO ' .$projecto->name. ' PARA REVISION PRELIMINAR';

            // Store the ProjectStatus
            $projectStatus = ProjectStatus::create($sanitized);

            try {
                if ($mailEnabled) {
                    Mail::mailer('mail2')->send('admin.project-status.emailDGJN', [
                        'proyecto' => $projecto->name ,
                        'id' => $projecto->id,
                        'sat' => $sat,
                        'satnombre' => $satnombre
                    ], function ($message) use ($userEmails, $subject) {
                        $message->to($userEmails);
                        $message->subject($subject);
                        $message->from('preseleccionfonavis@muvh.gov.py', env('APP_NAME'));
                    });
                }


                return response()->json([
                    'redirect' => url('admin/projects/' . $request['project_id'] . '/show')
                ]);
            } catch (Exception $e) {
                return response()->json([
                    'error' => 'No se pudo enviar el correo electrónico'
                ]);
            }
        }elseif ($sanitized['stage_id'] == 3) { // Estado APROBADO DGJN
            DB::beginTransaction();

            try {
                $proyecto = Project::findOrFail($request->input('project_id'));

                $sat = $proyecto->sat_id;
                $satnombre = Sat::where('NucCod', $sat)->first();
                $correoSat = User::where('sat_ruc', $sat)->value('email');

                $remitente = auth()->user();
                $correoDGJN = $remitente->email;

                if (!$satnombre) {
                    throw new \Exception("No se pudo obtener información del SAT o su correo.");
                }

                $projectStatus = ProjectStatus::create($sanitized);

                if ($mailEnabled) {
                    $toEmail = 'preseleccionfonavis@muvh.gov.py';
                    $subject = 'INFORME DGJN ' . $proyecto->name;

                    Mail::send(
                        'admin.project-status.emailDGJNAFONAVIS',
                        [
                            'proyecto' => $proyecto->name,
                            'id' => $proyecto->id,
                            'sat' => $sat,
                            'satnombre' => $satnombre->NucNomSat,
                        ],
                        function ($message) use ($toEmail, $subject, $correoSat) {
                            $message->to($toEmail);
                            $message->bcc($correoSat);
                            $message->subject($subject);
                            $message->from('sistema_fonavis@muvh.gov.py', env('APP_NAME'));
                        }
                    );

                    $subjectDGJN = "Envío de correo con Estado APROBADO DGJN a FONAVIS del proyecto: {$proyecto->name}";
                    $contenidoDGJN = "
                        Se notificó correctamente el siguiente proyecto:<br><br>
                        Proyecto: {$proyecto->name} ({$proyecto->id})<br>
                        SAT: {$satnombre->NucNomSat}<br><br>
                        Comentario registrado:<br>" . nl2br(htmlspecialchars($projectStatus->record));

                    Mail::mailer('smtp')->html($contenidoDGJN, function ($message) use ($correoDGJN, $subjectDGJN) {
                        $message->to($correoDGJN);
                        $message->from('sistema_fonavis@muvh.gov.py', 'DGJN - MUVH');
                        $message->subject($subjectDGJN);
                    });

                    if (count(Mail::failures()) > 0) {
                        throw new \Exception('Fallo al enviar el correo: ' . implode(', ', Mail::failures()));
                    }
                }

                DB::commit();

                return response()->json([
                    'redirect' => url('admin/projects/' . $request['project_id'] . '/showDGJN')
                ]);

            } catch (\Exception $e) {
                DB::rollBack();

                \Log::error('Error en etapa 3 (APROBADO DGJN): ' . $e->getMessage());

                return response()->json([
                    'error' => 'Error procesando la solicitud: ' . $e->getMessage()
                ], 500);
            }
        }elseif ($sanitized['stage_id'] == 4) { // Estado OBSERVADO DGJN
            DB::beginTransaction();

            try {
                $proyecto = Project::findOrFail($request->input('project_id'));
                $sat = $proyecto->sat_id;
                $satnombre = Sat::where('NucCod', $sat)->value('NucNomSat');
                $correoSat = User::where('sat_ruc', $sat)->value('email');
                $remitente = auth()->user();
                $correoDGJN = AdminUser::where('id', $remitente->id)->value('email');

                if (!$satnombre) {
                    throw new \Exception("No se encontró el nombre del SAT.");
                }

                $projectStatus = ProjectStatus::create($sanitized);

                if ($mailEnabled) {
                    $toEmails[] = 'preseleccionfonavis@muvh.gov.py';

                    $subject = 'INFORME DGJN EN ARCHIVO ' . $proyecto->name;

                    Mail::mailer('smtp')->send(
                        'admin.project-status.emailDGJNAFONAVISARCHIVADO',
                        [
                            'proyecto' => $proyecto->name,
                            'id' => $proyecto->id,
                            'sat' => $sat,
                            'satnombre' => $satnombre
                        ],
                        function ($message) use ($toEmails, $subject, $correoSat) {
                            $message->to($toEmails);
                            $message->bcc($correoSat);
                            $message->subject($subject);
                            $message->from('sistema_fonavis@muvh.gov.py', env('APP_NAME'));
                        }
                    );

                    if (count(Mail::failures()) > 0) {
                        throw new \Exception('Error al enviar el correo a: ' . implode(', ', Mail::failures()));
                    }

                    $subjectDGJN = "Envío de correo con Estado ARCHIVADO DGJN a FONAVIS del proyecto: {$proyecto->name}";
                    $contenidoDGJN = "
                        Se notificó correctamente el siguiente proyecto:<br><br>
                        Proyecto: {$proyecto->name} ({$proyecto->id})<br>
                        SAT: {$satnombre}<br><br>
                        Comentario registrado:<br>" . nl2br(htmlspecialchars($projectStatus->record));

                    Mail::mailer('smtp')->html($contenidoDGJN, function ($message) use ($correoDGJN, $subjectDGJN) {
                        $message->to($correoDGJN);
                        $message->from('sistema_fonavis@muvh.gov.py', 'DGJN - MUVH');
                        $message->subject($subjectDGJN);
                    });
                }

                DB::commit();

                return response()->json([
                    'redirect' => url('admin/projects/' . $request['project_id'] . '/showDGJN')
                ]);

            } catch (\Exception $e) {
                DB::rollBack();

                \Log::error('Error en etapa 4 (ARCHIVADO DGJN): ' . $e->getMessage());

                return response()->json([
                    'error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()
                ], 500);
            }
        }elseif ($sanitized['stage_id'] == 6) { // Estado RECHAZADO DGJN
            DB::beginTransaction();

            try {
                $proyecto = Project::findOrFail($request->input('project_id'));

                $sat = $proyecto->sat_id;
                $satnombre = Sat::where('NucCod', $sat)->value('NucNomSat');
                $correoSat = User::where('sat_ruc', $sat)->value('email');

                $remitente = auth()->user();
                $correoDGJN = $remitente->email;

                $projectStatus = ProjectStatus::create($sanitized);

                if ($mailEnabled) {
                    Mail::send(
                        'admin.project-status.emailDGJNAFONAVISRECHAZADO',
                        [
                            'proyecto' => $proyecto->name,
                            'id' => $proyecto->id,
                            'sat' => $sat,
                        ],
                        function ($message) use ($correoSat) {
                            $message->to('preseleccionfonavis@muvh.gov.py');
                            $message->bcc($correoSat);
                            $message->subject('INFORME PROYECTO RECHAZADO POR DGJN');
                            $message->from('sistema_fonavis@muvh.gov.py', env('APP_NAME'));
                        }
                    );

                    $subjectDGJN = "Envío de correo con Estado RECHAZADO DGJN a FONAVIS del proyecto: {$proyecto->name}";
                    $contenidoDGJN = "
                        Se notificó correctamente el siguiente proyecto:<br><br>
                        Proyecto: {$proyecto->name} ({$proyecto->id})<br>
                        SAT: {$satnombre}<br><br>
                        Comentario registrado:<br>" . nl2br(htmlspecialchars($projectStatus->record));

                    Mail::mailer('smtp')->html($contenidoDGJN, function ($message) use ($correoDGJN, $subjectDGJN) {
                        $message->to($correoDGJN);
                        $message->from('sistema_fonavis@muvh.gov.py', 'DGJN - MUVH');
                        $message->subject($subjectDGJN);
                    });

                    if (count(Mail::failures()) > 0) {
                        throw new \Exception('Fallo al enviar el correo: ' . implode(', ', Mail::failures()));
                    }
                }

                DB::commit();

                return response()->json([
                    'redirect' => url('admin/projects/' . $request['project_id'] . '/showDGJN')
                ]);

            } catch (\Exception $e) {
                DB::rollBack();

                \Log::error('Error en etapa 6 (RECHAZADO DGJN): ' . $e->getMessage());

                return response()->json([
                    'error' => 'Error procesando la solicitud: ' . $e->getMessage()
                ], 500);
            }
        }
        elseif ($sanitized['stage_id'] == 8) { // Estado GRUPO FAMILIAR ENVIADO

            DB::beginTransaction();

            try {
                $projecto = Project::where('id', $request->project_id)->first();
                if (!$projecto) {
                    return response()->json(['error' => 'No se encontró el proyecto']);
                }

                $sat = $projecto->sat_id;
                $useremail = User::where('sat_ruc', $sat)->select('email')->first();
                if (!$useremail) {
                    return response()->json(['error' => 'No se encontró email para el usuario SAT']);
                }

                $satnombre = Sat::where('NucCod', $sat)->select('NucNomSat')->first();
                if (!$satnombre) {
                    return response()->json(['error' => 'No se encontró el nombre del SAT']);
                }

                $dependenciaDGSO = AdminUsersDependency::where('dependency_id', 3)->pluck('admin_user_id');
                $usuarios = AdminUser::whereIn('id', $dependenciaDGSO)->get();

                $userEmails = $usuarios->pluck('email')->toArray();

                $toEmails = [$useremail->email];

                $allEmails = array_merge($userEmails, $toEmails);

                if (empty($allEmails)) {
                    return response()->json(['error' => 'No hay correos para enviar el mensaje']);
                }

                $subject = 'EVALUACION SOCIAL ' . $projecto->name;

                $projectStatus = ProjectStatus::create($sanitized);

                if ($mailEnabled) {
                    Mail::mailer('mail2')->send(
                        'admin.project-status.emailFONAVISDGSOSAT',
                        [
                            'proyecto' => $projecto->name,
                            'id' => $projecto->id,
                            'sat' => $sat,
                            'satnombre' => $satnombre->NucNomSat
                        ],
                        function ($message) use ($allEmails, $subject) {
                            $message->to($allEmails);
                            $message->subject($subject);
                            $message->from('preseleccionfonavis@muvh.gov.py', env('APP_NAME'));
                        }
                    );
                }

                DB::commit();

                return response()->json([
                    'redirect' => url('admin/projects/' . $request['project_id'] . '/showFONAVIS')
                ]);

            } catch (Exception $e) {
                DB::rollBack();

                return response()->json([
                    'error' => 'Error durante el proceso: ' . $e->getMessage()
                ]);
            }
        }elseif ($sanitized['stage_id'] == 9) { // Estado CON DICTAMEN SOCIAL

            DB::beginTransaction();

            try {
                $projecto = Project::where('id', $request->project_id)->first();
                if (!$projecto) {
                    return response()->json(['error' => 'No se encontró el proyecto']);
                }

                $sat = $projecto->sat_id;
                $useremail = 'preseleccionfonavis@muvh.gov.py';

                $satnombre = Sat::where('NucCod', $sat)->first();
                if (!$satnombre) {
                    return response()->json(['error' => 'No se encontró el nombre del SAT']);
                }

                $toEmail = $useremail;

                $subject = 'INFORME DGSO CON DICTAMEN SOCIAL ' . $projecto->name;

                $projectStatus = ProjectStatus::create($sanitized);

                if ($mailEnabled) {
                    Mail::mailer('smtp')->send(
                        'admin.project-status.emailDGSOAFONAVIS',
                        [
                            'proyecto' => $projecto->name,
                            'id' => $projecto->id,
                            'sat' => $sat,
                            'satnombre' => $satnombre->NucNomSat
                        ],
                        function ($message) use ($toEmail, $subject) {
                            $message->to($toEmail);
                            $message->subject($subject);
                            $message->from('sistema_fonavis@muvh.gov.py', env('APP_NAME'));
                        }
                    );
                }

                DB::commit();

                return response()->json([
                    'redirect' => url('admin/projects/' . $request['project_id'] . '/showDGSO')
                ]);

            } catch (Exception $e) {
                DB::rollBack();

                return response()->json([
                    'error' => 'Error durante el proceso: ' . $e->getMessage()
                ]);
            }
        }elseif ($sanitized['stage_id'] == 10) { // Estado EVALUACION TECNICA
            DB::beginTransaction();

            try {
                $projecto = Project::where('id', $request->project_id)->first();
                if (!$projecto) {
                    return response()->json(['error' => 'No se encontró el proyecto'], 404);
                }

                $sat = $projecto->sat_id;

                $useremailSAT = User::where('sat_ruc', $sat)->first();
                if (!$useremailSAT || !$useremailSAT->email) {
                    return response()->json(['error' => 'No se encontró email para el usuario SAT'], 404);
                }
                $useremailSAT = $useremailSAT->email;

                $satobtenernombre = Sat::where('NucCod', $sat)->first();
                $satnombre = $satobtenernombre?->NucNomSat;
                if (!$satnombre) {
                    return response()->json(['error' => 'No se encontró el nombre del SAT'], 404);
                }


                $toEmail = $useremailSAT;

                $subject = 'PARA EVALUACION TECNICA ' . $projecto->name;

                $projectStatus = ProjectStatus::create($sanitized);

                if ($mailEnabled) {
                    Mail::mailer('mail2')->send(
                        'admin.project-status.emailFONAVISSAT',
                        [
                            'proyecto' => $projecto->name,
                            'id' => $projecto->id,
                            'sat' => $sat,
                            'satnombre' => $satnombre
                        ],
                        function ($message) use ($toEmail, $subject) {
                            $message->to($toEmail);
                            $message->subject($subject);
                            $message->from('preseleccionfonavis@muvh.gov.py', env('APP_NAME'));
                        }
                    );
                }

                DB::commit();

                return response()->json([
                    'redirect' => url('admin/projects/' . $request['project_id'] . '/showFONAVISSOCIAL')
                ]);

            } catch (Exception $e) {
                DB::rollBack();
                return response()->json([
                    'error' => 'Error durante el proceso de evaluación técnica: ' . $e->getMessage()
                ], 500);
            }
        }elseif ($sanitized['stage_id'] == 12) { // Estado VERIFICACION TECNICO AMBIENTAL
            DB::beginTransaction();

            try {
                $projecto = Project::where('id', $request->project_id)->first();
                if (!$projecto) {
                    return response()->json(['error' => 'No se encontró el proyecto'], 404);
                }

                $sat = $projecto->sat_id;

                $satobtenernombre = Sat::where('NucCod', $sat)->first();
                $satnombre = $satobtenernombre?->NucNomSat;
                if (!$satnombre) {
                    return response()->json(['error' => 'No se encontró el nombre del SAT'], 404);
                }

                $dependenciaDIGH = AdminUsersDependency::where('dependency_id', 4)->pluck('admin_user_id');
                $usuariosDIGH = AdminUser::whereIn('id', $dependenciaDIGH)->get();
                $emailsDIGH = $usuariosDIGH->pluck('email')->toArray();

                if (empty($emailsDIGH)) {
                    return response()->json(['error' => 'No se encontraron usuarios de la dependencia DIGH para enviar el correo'], 404);
                }

                $subject = 'PARA VERIFICACION TECNICO AMBIENTAL ' . $projecto->name;

                $projectStatus = ProjectStatus::create($sanitized);

                if ($mailEnabled) {
                    Mail::mailer('mail2')->send(
                        'admin.project-status.emailFONAVISDIGH',
                        [
                            'proyecto' => $projecto->name,
                            'id' => $projecto->id,
                            'sat' => $sat,
                            'satnombre' => $satnombre
                        ],
                        function ($message) use ($emailsDIGH, $subject) {
                            $message->to($emailsDIGH);
                            $message->subject($subject);
                            $message->from('preseleccionfonavis@muvh.gov.py', env('APP_NAME'));
                        }
                    );
                }

                DB::commit();

                return response()->json([
                    'redirect' => url('admin/projects/' . $request['project_id'] . '/showFONAVISTECNICO')
                ]);

            } catch (Exception $e) {
                DB::rollBack();
                return response()->json([
                    'error' => 'Error durante el proceso de verificación técnico ambiental: ' . $e->getMessage()
                ], 500);
            }
        }elseif ($sanitized['stage_id'] == 13) { //Estado CON INFORME VTA
            $projecto = Project::where('id', $request->project_id)->get();
            $sat = $projecto[0]->sat_id;
            $useremail = User::where('sat_ruc', $sat)->get()->first();
            $satnombre = Sat::where('NucCod', $sat)->get()->first();

            $toEmails = [];

            if ($useremail) {
                $toEmails[] = $useremail->email;
            }

            $toEmails[] = 'preseleccionfonavis@muvh.gov.py';

            $subject = 'INFORME VTA '.$projecto[0]->name;

            $projectStatus = ProjectStatus::create($sanitized);

            try {
                if ($mailEnabled) {
                    Mail::send('admin.project-status.emailDIGHFONAVISSAT', ['proyecto' => $projecto[0]->name ,'id' => $projecto[0]->id,'sat' => $sat,'satnombre' => $satnombre], function ($message) use ($toEmails, $subject) {
                        $message->to($toEmails);
                        $message->subject($subject);
                        $message->from('sistema_fonavis@muvh.gov.py', env('APP_NAME'));
                    });
                }

                return response()->json([
                    'redirect' => url('admin/projects/' . $request['project_id'] . '/showDIGH')
                ]);
            } catch (Exception $e) {
                return response()->json([
                    'error' => 'No se pudo enviar el correo electrónico'
                ]);
            }
        }elseif ($sanitized['stage_id'] == 15) { //Estado RECHAZADO DIGH
            $projecto = Project::where('id', $request->project_id)->get();
            $sat = $projecto[0]->sat_id;
            $useremail = User::where('sat_ruc', $sat)->get()->first();
            $satnombre = Sat::where('NucCod', $sat)->get()->first();

            $toEmails = [];

            if ($useremail) {
                $toEmails[] = $useremail->email;
            }

            $toEmails[] = 'preseleccionfonavis@muvh.gov.py';

            $subject = 'RECHAZADO POR DIGH '.$projecto[0]->name;

            $projectStatus = ProjectStatus::create($sanitized);

            try {
                if ($mailEnabled) {
                    Mail::send('admin.project-status.emailDIGHFONAVISSATRECHAZADO', ['proyecto' => $projecto[0]->name ,'id' => $projecto[0]->id,'sat' => $sat,'satnombre' => $satnombre], function ($message) use ($toEmails, $subject) {
                        $message->to($toEmails);
                        $message->subject($subject);
                        $message->from('sistema_fonavis@muvh.gov.py', env('APP_NAME'));
                    });
                }

                return response()->json([
                    'redirect' => url('admin/projects/' . $request['project_id'] . '/showDIGH')
                ]);
            } catch (Exception $e) {
                return response()->json([
                    'error' => 'No se pudo enviar el correo electrónico'
                ]);
            }
        }elseif ($sanitized['stage_id'] == 16) { //Estado EVALUACION TECNICO HABITACIONAL;
            $projecto = Project::where('id', $request->project_id)->get();
            $sat = $projecto[0]->sat_id;
            $useremail = 'osemidei@gmail.com';
            $satnombre = Sat::where('NucCod', $sat)->get()->first();

            $toEmail = $useremail;

            $subject = 'PARA EVALUACION TECNICO HABITACIONAL '.$projecto[0]->name;

            $projectStatus = ProjectStatus::create($sanitized);

            try {
                if ($mailEnabled) {
                    Mail::mailer('mail2')->send('admin.project-status.emailFONAVISDSGO', ['proyecto' => $projecto[0]->name ,'id' => $projecto[0]->id,'sat' => $sat,'satnombre' => $satnombre], function ($message) use ($toEmail, $subject) {
                        $message->to($toEmail);
                        $message->subject($subject);
                        $message->from('preseleccionfonavis@muvh.gov.py', env('APP_NAME'));
                    });
                }

                return response()->json([
                    'redirect' => url('admin/projects/' . $request['project_id'] . '/showFONAVISTECNICODOS')
                ]);
            } catch (Exception $e) {
                return response()->json([
                    'error' => 'No se pudo enviar el correo electrónico'
                ]);
            }
        }elseif ($sanitized['stage_id'] == 17) { //Estado CON CALIFICACION TECNICA HABITACIONAL
            $projecto = Project::where('id', $request->project_id)->get();
            $sat = $projecto[0]->sat_id;
            $useremail1 = User::where('sat_ruc', $sat)->get()->first();
            $useremail = $useremail1->email;
            $satnombre = Sat::where('NucCod', $sat)->get()->first();

            $toEmails = [];

            if ($useremail) {
                $toEmails[] = $useremail;
            }

            $toEmails[] = 'preseleccionfonavis@muvh.gov.py';

            $subject = 'CALIFICACION TECNICA HABITACIONAL '.$projecto[0]->name;

            $projectStatus = ProjectStatus::create($sanitized);

            try {
                if ($mailEnabled) {
                    Mail::send('admin.project-status.emailFONAVISSATDOS', ['proyecto' => $projecto[0]->name ,'id' => $projecto[0]->id,'sat' => $sat,'satnombre' => $satnombre], function ($message) use ($toEmails, $subject) {
                        $message->to($toEmails);
                        $message->subject($subject);
                        $message->from('sistema_fonavis@muvh.gov.py', env('APP_NAME'));
                    });
                }

                return response()->json([
                    'redirect' => url('admin/projects/' . $request['project_id'] . '/showDSGO')
                ]);
            } catch (Exception $e) {
                return response()->json([
                    'error' => 'No se pudo enviar el correo electrónico'
                ]);
            }
        }elseif ($sanitized['stage_id'] == 18) { //Estado ADJUDICADO
            $projecto = Project::where('id', $request->project_id)->get();
            $sat = $projecto[0]->sat_id;
            $useremail1 = User::where('sat_ruc', $sat)->get()->first();
            $useremail = $useremail1->email;
            $satnombre = Sat::where('NucCod', $sat)->get()->first();

            $toEmails = [];

            if ($useremail) {
                $toEmails[] = $useremail;
            }

            $toEmails[] = 'osemidei@muvh.gov.py';
            $toEmails[] = 'nmorel@muvh.gov.py';
            $toEmails[] = 'osemidei@gmail.com';
            $toEmails[] = 'fleon@muvh.gov.py';

            $subject = 'ADJUDICACION DE PROYECTO '.$projecto[0]->name;

            $projectStatus = ProjectStatus::create($sanitized);

            try {
                if ($mailEnabled) {
                    Mail::mailer('mail2')->send('admin.project-status.emailFONAVISADJ', ['proyecto' => $projecto[0]->name ,'id' => $projecto[0]->id,'sat' => $sat,'satnombre' => $satnombre], function ($message) use ($toEmails, $subject) {
                        $message->to($toEmails);
                        $message->subject($subject);
                        $message->from('preseleccionfonavis@muvh.gov.py', env('APP_NAME'));
                    });
                }

                return response()->json([
                    'redirect' => url('admin/projects/' . $request['project_id'] . '/showFONAVIS')
                ]);
            } catch (Exception $e) {
                return response()->json([
                    'error' => 'No se pudo enviar el correo electrónico'
                ]);
            }
        }

        else {
            $projectStatus = ProjectStatus::create($sanitized);

            return response()->json([
                'redirect' => url('admin/projects/' . $request['project_id'] . '/show')
            ]);
        }

        return response()->json([
            'redirect' => url('admin/projects/' . $request['project_id'] . '/show')
        ]);
    }


    public function storeNotificacion(Request $request)
    {
        $request->validate([
            'project_id' => 'required|integer|exists:projects,id',
            'stage_id' => 'required|integer|exists:stages,id',
            'record' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Crear y guardar el nuevo registro en ProjectStatus
            $projectStatus = new ProjectStatus();
            $projectStatus->project_id = $request->input('project_id');
            $projectStatus->stage_id = $request->input('stage_id');
            $projectStatus->record = $request->input('record');
            $projectStatus->user_id = auth()->id();
            $projectStatus->save();

            // Obtener información relacionada
            $proyecto = Project::find($request->input('project_id'));
            $sat = $proyecto->sat_id;
            $satnombre = Sat::where('NucCod', $sat)->value('NucNomSat');
            $correoSat = User::where('sat_ruc', $sat)->value('email');
            $remitente = auth()->user();
            $correoDGJN = AdminUser::where('id', $remitente->id)->value('email');

            if ($request->input('stage_id') == 2 && $correoSat) {
                $subject = 'PROYECTO ' . $proyecto->name . ' - PRESENTACION DE DOCUMENTOS';
                $contenidoCorreo = "
                    Proyecto: {$proyecto->name} (ID: {$proyecto->id}) <br>
                    Nombre SAT: {$satnombre} (SAT: {$sat}) <br><br>
                    {$projectStatus->record}
                ";

                // Enviar correo al SAT
                Mail::mailer('smtp')->html($contenidoCorreo, function ($message) use ($correoSat, $subject) {
                    $message->to($correoSat);
                    $message->from('sistema_fonavis@muvh.gov.py', 'DGJN - MUVH');
                    $message->subject($subject);
                });

                // Enviar copia al remitente DGJN
                $subjectDGJN = "Notificaste al SAT: {$satnombre}";
                $contenidoDGJN = "
                    Proyecto: {$proyecto->name} ({$proyecto->id})<br><br>
                    Notificaste al correo({$correoSat}) registrado por SAT, lo siguiente: <br><br>
                    {$projectStatus->record}
                ";

                Mail::mailer('smtp')->html($contenidoDGJN, function ($message) use ($correoDGJN, $subjectDGJN) {
                    $message->to($correoDGJN);
                    $message->from('sistema_fonavis@muvh.gov.py', 'DGJN - MUVH');
                    $message->subject($subjectDGJN);
                });
            }

            DB::commit();

            return response()->json([
                'redirect' => url('admin/projects/' . $request->input('project_id') . '/showDGJN'),
                'message' => 'Estado del proyecto actualizado correctamente y correo enviado.'
            ]);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'error' => 'Error durante la operación: ' . $e->getMessage()
            ], 500);
        }
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
