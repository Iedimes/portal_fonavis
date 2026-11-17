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
     * Envía un correo de vista Blade manejando límites de tasa SMTP (421) y evitando bloquear la transacción.
     */
    private function sendViewMailSafely(string $primaryMailer, string $view, array $data, callable $callback, string $context = ''): void
    {
        try {
            Mail::mailer($primaryMailer)->send($view, $data, $callback);
        } catch (\Throwable $e) {
            \Log::warning("Error SMTP primario ({$primaryMailer}) en {$context}: " . $e->getMessage());

            // Si falla por rate-limit (421) u otro error de transporte, intentar con mailer secundario tras breve pausa
            $fallbackMailer = $primaryMailer === 'smtp' ? 'mail2' : 'smtp';
            $fallbackFrom = $fallbackMailer === 'mail2' ? 'preseleccionfonavis@muvh.gov.py' : 'sistema_fonavis@muvh.gov.py';

            try {
                usleep(600000); // 0.6s pausa para aliviar rate limit del servidor SMTP
                Mail::mailer($fallbackMailer)->send($view, $data, function ($message) use ($callback, $fallbackFrom) {
                    $callback($message);
                    $message->from($fallbackFrom, env('APP_NAME'));
                });
            } catch (\Throwable $fallbackEx) {
                \Log::error("No se pudo enviar correo (fallback {$fallbackMailer} también falló) en {$context}: " . $fallbackEx->getMessage());
            }
        }
    }

    /**
     * Envía un correo HTML directo manejando límites de tasa SMTP (421) y evitando bloquear la transacción.
     */
    private function sendHtmlMailSafely(string $primaryMailer, string $html, callable $callback, string $context = ''): void
    {
        try {
            usleep(500000); // 0.5s pausa cuando se envía un segundo correo consecutivo
            Mail::mailer($primaryMailer)->html($html, $callback);
        } catch (\Throwable $e) {
            \Log::warning("Error SMTP HTML primario ({$primaryMailer}) en {$context}: " . $e->getMessage());

            $fallbackMailer = $primaryMailer === 'smtp' ? 'mail2' : 'smtp';
            $fallbackFrom = $fallbackMailer === 'mail2' ? 'preseleccionfonavis@muvh.gov.py' : 'sistema_fonavis@muvh.gov.py';

            try {
                usleep(600000);
                Mail::mailer($fallbackMailer)->html($html, function ($message) use ($callback, $fallbackFrom) {
                    $callback($message);
                    $message->from($fallbackFrom, 'DGJN - MUVH');
                });
            } catch (\Throwable $fallbackEx) {
                \Log::error("No se pudo enviar correo HTML (fallback {$fallbackMailer} también falló) en {$context}: " . $fallbackEx->getMessage());
            }
        }
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

        // Obtener el último estado del proyecto
        $ultimoEstado = ProjectStatus::where('project_id', $request->project_id)
                            ->orderBy('created_at', 'desc')
                            ->first();

        if ($ultimoEstado && $ultimoEstado->stage_id == $sanitized['stage_id']) {
            return response()->json([
                'success' => false,
                'redirect' => route('adminprojectsindex'),
                'message' => 'No se puede registrar el mismo estado consecutivo.'
            ]);
        }

        // DGFO no puede actuar sobre el estado OBSERVADO DGJN
        if ($ultimoEstado && $ultimoEstado->stage_id == 4 && $dependencia == 1) {
            return response()->json([
                'success' => false,
                'redirect' => route('adminprojectsindex'),
                'message' => 'DGFO no tiene autorización para actuar sobre proyectos en estado OBSERVADO DGJN.'
            ], 403);
        }

        // Variable para controlar envío de correos
        $mailEnabled = env('MAIL_ENABLED', true);

        $projecto = Project::where('id', $request->project_id)->first();
        if (!$projecto) {
            return response()->json(['error' => 'No se encontró el proyecto'], 404);
        }

        if (config('app.dgso_planilla_calificacion_enabled') && $ultimoEstado && $ultimoEstado->stage_id == 8 && (!$projecto->calificacion_finalizada || !$projecto->shd_migrated)) {
            return response()->json([
                'success' => false,
                'redirect' => url('admin/projects/' . $projecto->id . '/showDGSO'),
                'message' => 'Debe finalizar la calificación y migrar a SHD antes de cambiar de estado.'
            ], 422);
        }

        $sat = $projecto->sat_id;
        $satnombre = Sat::where('NucCod', $sat)->select('NucNomSat')->first();


        if ($sanitized['stage_id'] == 2) {

            $dependenciaDGJN = AdminUsersDependency::where('dependency_id', 2)
                                                ->pluck('admin_user_id');

            $usuarios = AdminUser::whereIn('id', $dependenciaDGJN)->get();

            $userEmails = $usuarios->pluck('email')->filter()->toArray();
            $subject = 'PROYECTO ' . $projecto->name . ' PARA REVISION PRELIMINAR';

            // Store the ProjectStatus
            $projectStatus = ProjectStatus::create($sanitized);

            if ($mailEnabled && !empty($userEmails)) {
                $this->sendViewMailSafely(
                    'mail2',
                    'admin.project-status.emailDGJN',
                    [
                        'proyecto' => $projecto->name,
                        'id' => $projecto->id,
                        'sat' => $sat,
                        'satnombre' => $satnombre
                    ],
                    function ($message) use ($userEmails, $subject) {
                        $message->to($userEmails);
                        $message->subject($subject);
                        $message->from('preseleccionfonavis@muvh.gov.py', env('APP_NAME'));
                    },
                    'Etapa 2 (REVISION PRELIMINAR)'
                );
            }

            return response()->json([
                'redirect' => url('admin/projects/' . $request['project_id'] . '/show')
            ]);

        } elseif ($sanitized['stage_id'] == 3) { // Estado APROBADO DGJN
            DB::beginTransaction();

            try {
                $proyecto = Project::findOrFail($request->input('project_id'));
                $projectStatus = ProjectStatus::create($sanitized);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Error guardando etapa 3 (APROBADO DGJN): ' . $e->getMessage());

                return response()->json([
                    'error' => 'Error procesando la solicitud: ' . $e->getMessage()
                ], 500);
            }

            if ($mailEnabled) {
                $sat = $proyecto->sat_id;
                $satObj = Sat::where('NucCod', $sat)->first();
                $satNombreStr = $satObj ? $satObj->NucNomSat : '';
                $correoSat = User::where('sat_ruc', $sat)->value('email');
                $remitente = auth()->user();
                $correoDGJN = $remitente ? $remitente->email : null;

                $toEmail = 'preseleccionfonavis@muvh.gov.py';
                $subject = 'INFORME DGJN ' . $proyecto->name;

                $this->sendViewMailSafely(
                    'smtp',
                    'admin.project-status.emailDGJNAFONAVIS',
                    [
                        'proyecto' => $proyecto->name,
                        'id' => $proyecto->id,
                        'sat' => $sat,
                        'satnombre' => $satNombreStr,
                    ],
                    function ($message) use ($toEmail, $subject, $correoSat) {
                        $message->to($toEmail);
                        if (!empty($correoSat)) {
                            $message->bcc($correoSat);
                        }
                        $message->subject($subject);
                        $message->from('sistema_fonavis@muvh.gov.py', env('APP_NAME'));
                    },
                    'Etapa 3 (APROBADO DGJN -> FONAVIS)'
                );

                if (!empty($correoDGJN)) {
                    $subjectDGJN = "Envío de correo con Estado APROBADO DGJN a FONAVIS del proyecto: {$proyecto->name}";
                    $contenidoDGJN = "
                        Se notificó correctamente el siguiente proyecto:<br><br>
                        Proyecto: {$proyecto->name} ({$proyecto->id})<br>
                        SAT: {$satNombreStr}<br><br>
                        Comentario registrado:<br>" . nl2br(htmlspecialchars($projectStatus->record));

                    $this->sendHtmlMailSafely(
                        'mail2',
                        $contenidoDGJN,
                        function ($message) use ($correoDGJN, $subjectDGJN) {
                            $message->to($correoDGJN);
                            $message->from('preseleccionfonavis@muvh.gov.py', 'DGJN - MUVH');
                            $message->subject($subjectDGJN);
                        },
                        'Etapa 3 (Confirmación DGJN)'
                    );
                }
            }

            return response()->json([
                'redirect' => url('admin/projects/' . $request['project_id'] . '/showDGJN')
            ]);

        } elseif ($sanitized['stage_id'] == 4) { // Estado OBSERVADO DGJN
            DB::beginTransaction();

            try {
                $proyecto = Project::findOrFail($request->input('project_id'));
                $projectStatus = ProjectStatus::create($sanitized);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Error guardando etapa 4 (ARCHIVADO/OBSERVADO DGJN): ' . $e->getMessage());

                return response()->json([
                    'error' => 'Ocurrió un error al procesar la solicitud: ' . $e->getMessage()
                ], 500);
            }

            if ($mailEnabled) {
                $sat = $proyecto->sat_id;
                $satNombreStr = Sat::where('NucCod', $sat)->value('NucNomSat') ?? '';
                $correoSat = User::where('sat_ruc', $sat)->value('email');
                $remitente = auth()->user();
                $correoDGJN = $remitente ? AdminUser::where('id', $remitente->id)->value('email') : null;

                $toEmails = ['preseleccionfonavis@muvh.gov.py'];
                $subject = 'INFORME DGJN EN ARCHIVO ' . $proyecto->name;

                $this->sendViewMailSafely(
                    'smtp',
                    'admin.project-status.emailDGJNAFONAVISARCHIVADO',
                    [
                        'proyecto' => $proyecto->name,
                        'id' => $proyecto->id,
                        'sat' => $sat,
                        'satnombre' => $satNombreStr
                    ],
                    function ($message) use ($toEmails, $subject, $correoSat) {
                        $message->to($toEmails);
                        if (!empty($correoSat)) {
                            $message->bcc($correoSat);
                        }
                        $message->subject($subject);
                        $message->from('sistema_fonavis@muvh.gov.py', env('APP_NAME'));
                    },
                    'Etapa 4 (OBSERVADO DGJN -> FONAVIS)'
                );

                if (!empty($correoDGJN)) {
                    $subjectDGJN = "Envío de correo con Estado ARCHIVADO DGJN a FONAVIS del proyecto: {$proyecto->name}";
                    $contenidoDGJN = "
                        Se notificó correctamente el siguiente proyecto:<br><br>
                        Proyecto: {$proyecto->name} ({$proyecto->id})<br>
                        SAT: {$satNombreStr}<br><br>
                        Comentario registrado:<br>" . nl2br(htmlspecialchars($projectStatus->record));

                    $this->sendHtmlMailSafely(
                        'mail2',
                        $contenidoDGJN,
                        function ($message) use ($correoDGJN, $subjectDGJN) {
                            $message->to($correoDGJN);
                            $message->from('preseleccionfonavis@muvh.gov.py', 'DGJN - MUVH');
                            $message->subject($subjectDGJN);
                        },
                        'Etapa 4 (Confirmación DGJN)'
                    );
                }
            }

            return response()->json([
                'redirect' => url('admin/projects/' . $request['project_id'] . '/showDGJN')
            ]);

        } elseif ($sanitized['stage_id'] == 6) { // Estado RECHAZADO DGJN
            DB::beginTransaction();

            try {
                $proyecto = Project::findOrFail($request->input('project_id'));
                $projectStatus = ProjectStatus::create($sanitized);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Error guardando etapa 6 (RECHAZADO DGJN): ' . $e->getMessage());

                return response()->json([
                    'error' => 'Error procesando la solicitud: ' . $e->getMessage()
                ], 500);
            }

            if ($mailEnabled) {
                $sat = $proyecto->sat_id;
                $satNombreStr = Sat::where('NucCod', $sat)->value('NucNomSat') ?? '';
                $correoSat = User::where('sat_ruc', $sat)->value('email');
                $remitente = auth()->user();
                $correoDGJN = $remitente ? $remitente->email : null;

                $this->sendViewMailSafely(
                    'smtp',
                    'admin.project-status.emailDGJNAFONAVISRECHAZADO',
                    [
                        'proyecto' => $proyecto->name,
                        'id' => $proyecto->id,
                        'sat' => $sat,
                    ],
                    function ($message) use ($correoSat) {
                        $message->to('preseleccionfonavis@muvh.gov.py');
                        if (!empty($correoSat)) {
                            $message->bcc($correoSat);
                        }
                        $message->subject('INFORME PROYECTO RECHAZADO POR DGJN');
                        $message->from('sistema_fonavis@muvh.gov.py', env('APP_NAME'));
                    },
                    'Etapa 6 (RECHAZADO DGJN -> FONAVIS)'
                );

                if (!empty($correoDGJN)) {
                    $subjectDGJN = "Envío de correo con Estado RECHAZADO DGJN a FONAVIS del proyecto: {$proyecto->name}";
                    $contenidoDGJN = "
                        Se notificó correctamente el siguiente proyecto:<br><br>
                        Proyecto: {$proyecto->name} ({$proyecto->id})<br>
                        SAT: {$satNombreStr}<br><br>
                        Comentario registrado:<br>" . nl2br(htmlspecialchars($projectStatus->record));

                    $this->sendHtmlMailSafely(
                        'mail2',
                        $contenidoDGJN,
                        function ($message) use ($correoDGJN, $subjectDGJN) {
                            $message->to($correoDGJN);
                            $message->from('preseleccionfonavis@muvh.gov.py', 'DGJN - MUVH');
                            $message->subject($subjectDGJN);
                        },
                        'Etapa 6 (Confirmación DGJN)'
                    );
                }
            }

            return response()->json([
                'redirect' => url('admin/projects/' . $request['project_id'] . '/showDGJN')
            ]);

        } elseif ($sanitized['stage_id'] == 8) { // Estado GRUPO FAMILIAR ENVIADO

            DB::beginTransaction();

            try {
                $projecto = Project::where('id', $request->project_id)->first();
                if (!$projecto) {
                    return response()->json(['error' => 'No se encontró el proyecto'], 404);
                }

                $projectStatus = ProjectStatus::create($sanitized);
                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();

                return response()->json([
                    'error' => 'Error durante el proceso: ' . $e->getMessage()
                ], 500);
            }

            if ($mailEnabled) {
                $sat = $projecto->sat_id;
                $useremail = User::where('sat_ruc', $sat)->select('email')->first();
                $satnombre = Sat::where('NucCod', $sat)->select('NucNomSat')->first();

                $dependenciaDGSO = AdminUsersDependency::where('dependency_id', 3)->pluck('admin_user_id');
                $usuarios = AdminUser::whereIn('id', $dependenciaDGSO)->get();
                $userEmails = $usuarios->pluck('email')->filter()->toArray();

                $toEmails = ($useremail && $useremail->email) ? [$useremail->email] : [];
                $allEmails = array_values(array_unique(array_filter(array_merge($userEmails, $toEmails))));

                if (!empty($allEmails)) {
                    $subject = 'EVALUACION SOCIAL ' . $projecto->name;
                    $this->sendViewMailSafely(
                        'mail2',
                        'admin.project-status.emailFONAVISDGSOSAT',
                        [
                            'proyecto' => $projecto->name,
                            'id' => $projecto->id,
                            'sat' => $sat,
                            'satnombre' => $satnombre ? $satnombre->NucNomSat : ''
                        ],
                        function ($message) use ($allEmails, $subject) {
                            $message->to($allEmails);
                            $message->subject($subject);
                            $message->from('preseleccionfonavis@muvh.gov.py', env('APP_NAME'));
                        },
                        'Etapa 8 (GRUPO FAMILIAR ENVIADO)'
                    );
                }
            }

            return response()->json([
                'redirect' => url('admin/projects/' . $request['project_id'] . '/showFONAVIS')
            ]);

        } elseif ($sanitized['stage_id'] == 9) { // Estado CON DICTAMEN SOCIAL

            DB::beginTransaction();

            try {
                $projecto = Project::where('id', $request->project_id)->first();
                if (!$projecto) {
                    return response()->json(['error' => 'No se encontró el proyecto'], 404);
                }

                $projectStatus = ProjectStatus::create($sanitized);
                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();

                return response()->json([
                    'error' => 'Error durante el proceso: ' . $e->getMessage()
                ], 500);
            }

            if ($mailEnabled) {
                $sat = $projecto->sat_id;
                $toEmail = 'preseleccionfonavis@muvh.gov.py';
                $satnombre = Sat::where('NucCod', $sat)->first();
                $subject = 'INFORME DGSO CON DICTAMEN SOCIAL ' . $projecto->name;

                $this->sendViewMailSafely(
                    'smtp',
                    'admin.project-status.emailDGSOAFONAVIS',
                    [
                        'proyecto' => $projecto->name,
                        'id' => $projecto->id,
                        'sat' => $sat,
                        'satnombre' => $satnombre ? $satnombre->NucNomSat : ''
                    ],
                    function ($message) use ($toEmail, $subject) {
                        $message->to($toEmail);
                        $message->subject($subject);
                        $message->from('sistema_fonavis@muvh.gov.py', env('APP_NAME'));
                    },
                    'Etapa 9 (CON DICTAMEN SOCIAL)'
                );
            }

            return response()->json([
                'redirect' => url('admin/projects/' . $request['project_id'] . '/showDGSO')
            ]);

        } elseif ($sanitized['stage_id'] == 10) { // Estado EVALUACION TECNICA
            DB::beginTransaction();

            try {
                $projecto = Project::where('id', $request->project_id)->first();
                if (!$projecto) {
                    return response()->json(['error' => 'No se encontró el proyecto'], 404);
                }

                $projectStatus = ProjectStatus::create($sanitized);
                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                return response()->json([
                    'error' => 'Error durante el proceso de evaluación técnica: ' . $e->getMessage()
                ], 500);
            }

            if ($mailEnabled) {
                $sat = $projecto->sat_id;
                $useremailSAT = User::where('sat_ruc', $sat)->value('email');
                $satobtenernombre = Sat::where('NucCod', $sat)->first();
                $satnombre = $satobtenernombre?->NucNomSat ?? '';

                if (!empty($useremailSAT)) {
                    $toEmail = $useremailSAT;
                    $subject = 'PARA EVALUACION TECNICA ' . $projecto->name;

                    $this->sendViewMailSafely(
                        'mail2',
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
                        },
                        'Etapa 10 (EVALUACION TECNICA)'
                    );
                }
            }

            return response()->json([
                'redirect' => url('admin/projects/' . $request['project_id'] . '/showFONAVISSOCIAL')
            ]);

        } elseif ($sanitized['stage_id'] == 12) { // Estado VERIFICACION TECNICO AMBIENTAL
            DB::beginTransaction();

            try {
                $projecto = Project::where('id', $request->project_id)->first();
                if (!$projecto) {
                    return response()->json(['error' => 'No se encontró el proyecto'], 404);
                }

                $projectStatus = ProjectStatus::create($sanitized);
                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                return response()->json([
                    'error' => 'Error durante el proceso de verificación técnico ambiental: ' . $e->getMessage()
                ], 500);
            }

            if ($mailEnabled) {
                $sat = $projecto->sat_id;
                $satobtenernombre = Sat::where('NucCod', $sat)->first();
                $satnombre = $satobtenernombre?->NucNomSat ?? '';

                $dependenciaDIGH = AdminUsersDependency::where('dependency_id', 4)->pluck('admin_user_id');
                $usuariosDIGH = AdminUser::whereIn('id', $dependenciaDIGH)->get();
                $emailsDIGH = $usuariosDIGH->pluck('email')->filter()->toArray();

                if (!empty($emailsDIGH)) {
                    $subject = 'PARA VERIFICACION TECNICO AMBIENTAL ' . $projecto->name;

                    $this->sendViewMailSafely(
                        'mail2',
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
                        },
                        'Etapa 12 (VERIFICACION TECNICO AMBIENTAL)'
                    );
                }
            }

            return response()->json([
                'redirect' => url('admin/projects/' . $request['project_id'] . '/showFONAVISTECNICO')
            ]);

        } elseif ($sanitized['stage_id'] == 13) { // Estado CON INFORME VTA
            $projecto = Project::where('id', $request->project_id)->get();
            $sat = $projecto[0]->sat_id;
            $useremail = User::where('sat_ruc', $sat)->get()->first();
            $satnombre = Sat::where('NucCod', $sat)->get()->first();

            $toEmails = [];

            if ($useremail && $useremail->email) {
                $toEmails[] = $useremail->email;
            }

            $toEmails[] = 'preseleccionfonavis@muvh.gov.py';

            $subject = 'INFORME VTA ' . $projecto[0]->name;

            $projectStatus = ProjectStatus::create($sanitized);

            if ($mailEnabled) {
                $this->sendViewMailSafely(
                    'smtp',
                    'admin.project-status.emailDIGHFONAVISSAT',
                    [
                        'proyecto' => $projecto[0]->name,
                        'id' => $projecto[0]->id,
                        'sat' => $sat,
                        'satnombre' => $satnombre
                    ],
                    function ($message) use ($toEmails, $subject) {
                        $message->to($toEmails);
                        $message->subject($subject);
                        $message->from('sistema_fonavis@muvh.gov.py', env('APP_NAME'));
                    },
                    'Etapa 13 (CON INFORME VTA)'
                );
            }

            return response()->json([
                'redirect' => url('admin/projects/' . $request['project_id'] . '/showDIGH')
            ]);

        } elseif ($sanitized['stage_id'] == 15) { // Estado RECHAZADO DIGH
            $projecto = Project::where('id', $request->project_id)->get();
            $sat = $projecto[0]->sat_id;
            $useremail = User::where('sat_ruc', $sat)->get()->first();
            $satnombre = Sat::where('NucCod', $sat)->get()->first();

            $toEmails = [];

            if ($useremail && $useremail->email) {
                $toEmails[] = $useremail->email;
            }

            $toEmails[] = 'preseleccionfonavis@muvh.gov.py';

            $subject = 'RECHAZADO POR DIGH ' . $projecto[0]->name;

            $projectStatus = ProjectStatus::create($sanitized);

            if ($mailEnabled) {
                $this->sendViewMailSafely(
                    'smtp',
                    'admin.project-status.emailDIGHFONAVISSATRECHAZADO',
                    [
                        'proyecto' => $projecto[0]->name,
                        'id' => $projecto[0]->id,
                        'sat' => $sat,
                        'satnombre' => $satnombre
                    ],
                    function ($message) use ($toEmails, $subject) {
                        $message->to($toEmails);
                        $message->subject($subject);
                        $message->from('sistema_fonavis@muvh.gov.py', env('APP_NAME'));
                    },
                    'Etapa 15 (RECHAZADO DIGH)'
                );
            }

            return response()->json([
                'redirect' => url('admin/projects/' . $request['project_id'] . '/showDIGH')
            ]);

        } elseif ($sanitized['stage_id'] == 16) { // Estado EVALUACION TECNICO HABITACIONAL
            $projecto = Project::where('id', $request->project_id)->get();
            $sat = $projecto[0]->sat_id;
            $useremail = 'osemidei@gmail.com';
            $satnombre = Sat::where('NucCod', $sat)->get()->first();

            $toEmail = $useremail;

            $subject = 'PARA EVALUACION TECNICO HABITACIONAL ' . $projecto[0]->name;

            $projectStatus = ProjectStatus::create($sanitized);

            if ($mailEnabled) {
                $this->sendViewMailSafely(
                    'mail2',
                    'admin.project-status.emailFONAVISDSGO',
                    [
                        'proyecto' => $projecto[0]->name,
                        'id' => $projecto[0]->id,
                        'sat' => $sat,
                        'satnombre' => $satnombre
                    ],
                    function ($message) use ($toEmail, $subject) {
                        $message->to($toEmail);
                        $message->subject($subject);
                        $message->from('preseleccionfonavis@muvh.gov.py', env('APP_NAME'));
                    },
                    'Etapa 16 (EVALUACION TECNICO HABITACIONAL)'
                );
            }

            return response()->json([
                'redirect' => url('admin/projects/' . $request['project_id'] . '/showFONAVISTECNICODOS')
            ]);

        } elseif ($sanitized['stage_id'] == 17 || $sanitized['stage_id'] == 19) { // Estado CON CALIFICACION TECNICA HABITACIONAL
            $projecto = Project::where('id', $request->project_id)->get();
            $sat = $projecto[0]->sat_id;
            $useremail1 = User::where('sat_ruc', $sat)->get()->first();
            $useremail = $useremail1 ? $useremail1->email : null;
            $satnombre = Sat::where('NucCod', $sat)->get()->first();

            $toEmails = [];

            if ($useremail) {
                $toEmails[] = $useremail;
            }

            $toEmails[] = 'preseleccionfonavis@muvh.gov.py';

            $subject = 'CALIFICACION TECNICA HABITACIONAL ' . $projecto[0]->name;

            $projectStatus = ProjectStatus::create($sanitized);

            if ($mailEnabled) {
                $this->sendViewMailSafely(
                    'smtp',
                    'admin.project-status.emailFONAVISSATDOS',
                    [
                        'proyecto' => $projecto[0]->name,
                        'id' => $projecto[0]->id,
                        'sat' => $sat,
                        'satnombre' => $satnombre
                    ],
                    function ($message) use ($toEmails, $subject) {
                        $message->to($toEmails);
                        $message->subject($subject);
                        $message->from('sistema_fonavis@muvh.gov.py', env('APP_NAME'));
                    },
                    'Etapa 17/19 (CALIFICACION TECNICA HABITACIONAL)'
                );
            }

            return response()->json([
                'redirect' => url('admin/projects/' . $request['project_id'] . '/showDSGO')
            ]);

        } elseif ($sanitized['stage_id'] == 18) { // Estado ADJUDICADO
            $projecto = Project::where('id', $request->project_id)->get();
            $sat = $projecto[0]->sat_id;
            $useremail1 = User::where('sat_ruc', $sat)->get()->first();
            $useremail = $useremail1 ? $useremail1->email : null;
            $satnombre = Sat::where('NucCod', $sat)->get()->first();

            $toEmails = [];

            if ($useremail) {
                $toEmails[] = $useremail;
            }

            $toEmails[] = 'osemidei@muvh.gov.py';
            $toEmails[] = 'nmorel@muvh.gov.py';
            $toEmails[] = 'osemidei@gmail.com';
            $toEmails[] = 'fleon@muvh.gov.py';

            $subject = 'ADJUDICACION DE PROYECTO ' . $projecto[0]->name;

            $projectStatus = ProjectStatus::create($sanitized);

            if ($mailEnabled) {
                $this->sendViewMailSafely(
                    'mail2',
                    'admin.project-status.emailFONAVISADJ',
                    [
                        'proyecto' => $projecto[0]->name,
                        'id' => $projecto[0]->id,
                        'sat' => $sat,
                        'satnombre' => $satnombre
                    ],
                    function ($message) use ($toEmails, $subject) {
                        $message->to($toEmails);
                        $message->subject($subject);
                        $message->from('preseleccionfonavis@muvh.gov.py', env('APP_NAME'));
                    },
                    'Etapa 18 (ADJUDICADO)'
                );
            }

            return response()->json([
                'redirect' => url('admin/projects/' . $request['project_id'] . '/showFONAVIS')
            ]);
        } else {
            $projectStatus = ProjectStatus::create($sanitized);

            return response()->json([
                'redirect' => url('admin/projects/' . $request['project_id'] . '/show')
            ]);
        }
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

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'error' => 'Error durante la operación: ' . $e->getMessage()
            ], 500);
        }

        $mailEnabled = env('MAIL_ENABLED', true);

        if ($mailEnabled) {
            // Obtener información relacionada
            $proyecto = Project::find($request->input('project_id'));
            if ($proyecto) {
                $sat = $proyecto->sat_id;
                $satnombre = Sat::where('NucCod', $sat)->value('NucNomSat') ?? '';
                $correoSat = User::where('sat_ruc', $sat)->value('email');
                $remitente = auth()->user();
                $correoDGJN = $remitente ? AdminUser::where('id', $remitente->id)->value('email') : null;

                if ($request->input('stage_id') == 2 && !empty($correoSat)) {
                    $subject = 'PROYECTO ' . $proyecto->name . ' - PRESENTACION DE DOCUMENTOS';
                    $contenidoCorreo = "
                        Proyecto: {$proyecto->name} (ID: {$proyecto->id}) <br>
                        Nombre SAT: {$satnombre} (SAT: {$sat}) <br><br>
                        {$projectStatus->record}
                    ";

                    // Enviar correo al SAT
                    $this->sendHtmlMailSafely(
                        'smtp',
                        $contenidoCorreo,
                        function ($message) use ($correoSat, $subject) {
                            $message->to($correoSat);
                            $message->from('sistema_fonavis@muvh.gov.py', 'DGJN - MUVH');
                            $message->subject($subject);
                        },
                        'Notificación SAT (Etapa 2)'
                    );

                    // Enviar copia al remitente DGJN usando mail2 para no saturar la tasa de envío de sistema_fonavis
                    if (!empty($correoDGJN)) {
                        $subjectDGJN = "Notificaste al SAT: {$satnombre}";
                        $contenidoDGJN = "
                            Proyecto: {$proyecto->name} ({$proyecto->id})<br><br>
                            Notificaste al correo({$correoSat}) registrado por SAT, lo siguiente: <br><br>
                            {$projectStatus->record}
                        ";

                        $this->sendHtmlMailSafely(
                            'mail2',
                            $contenidoDGJN,
                            function ($message) use ($correoDGJN, $subjectDGJN) {
                                $message->to($correoDGJN);
                                $message->from('preseleccionfonavis@muvh.gov.py', 'DGJN - MUVH');
                                $message->subject($subjectDGJN);
                            },
                            'Copia Notificación DGJN (Etapa 2)'
                        );
                    }
                }
            }
        }

        return response()->json([
            'redirect' => url('admin/projects/' . $request->input('project_id') . '/showDGJN'),
            'message' => 'Estado del proyecto actualizado correctamente y correo enviado.'
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
        $project = Project::find($projectId);
        if ($project && $project->getEstados()->count() > 1) {
            return response()->json([
                'error' => 'El proyecto ya tiene más de un registro de estados. No se puede volver a Pendiente; debe utilizar la opción DEVOLUCIÓN SAT.'
            ], 422);
        }

        $projectStatus = ProjectStatus::where('project_id', $projectId)->first();
        if (!$projectStatus) {
            return response()->json([
                'error' => 'No se encontró registro de estado para este proyecto.'
            ], 404);
        }

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
