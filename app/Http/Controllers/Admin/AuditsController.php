<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OwenIt\Auditing\Models\Audit;
use Carbon\Carbon;

class AuditsController extends Controller
{
    /**
     * Mapeo de modelos a nombres legibles en español
     */
    public static $modelLabels = [
        'App\Models\Project' => 'Proyecto',
        'App\Models\ProjectStatus' => 'Estado de Proyecto (MUVH)',
        'App\Models\ProjectStatusF' => 'Estado de Proyecto (SAT)',
        'App\Models\ProjectStatusDeletes' => 'Estado Eliminado',
        'App\Models\Postulante' => 'Postulante',
        'App\Models\PostulanteHasBeneficiary' => 'Familiar / Beneficiario',
        'App\Models\PostulanteHasDiscapacidad' => 'Discapacidad Postulante',
        'App\Models\ProjectHasPostulantes' => 'Asignación Postulante a Proyecto',
        'App\Models\ProjectHasExpediente' => 'Expediente de Proyecto',
        'App\Models\Documents' => 'Documento de Proyecto',
        'App\Models\Documentsmissing' => 'Documento Faltante',
        'App\Models\DocumentCheck' => 'Checklist de Documentos',
        'App\Models\DighObservation' => 'Observación DIGH / DSGO',
        'App\Models\Comentario' => 'Comentario',
        'App\Models\Motivo' => 'Motivo de Rechazo / Eliminación',
        'App\Models\User' => 'Usuario SAT',
        'App\Models\AdminUser' => 'Usuario MUVH (Admin)',
        'App\Models\AdminUsersDependency' => 'Dependencia de Usuario',
        'App\Models\Land_project' => 'Terreno de Proyecto',
        'App\Models\LandHasProjectType' => 'Tipo de Proyecto / Terreno',
        'App\Models\Assignment' => 'Asignación Documental',
        'App\Models\Dependency' => 'Dependencia',
        'App\Models\Category' => 'Categoría de Documento',
        'App\Models\Document' => 'Tipo de Documento (Catálogo)',
        'App\Models\Discapacidad' => 'Tipo de Discapacidad',
        'App\Models\Land' => 'Tipo de Terreno (Catálogo)',
        'App\Models\Modality' => 'Modalidad',
        'App\Models\ModalityHasLand' => 'Modalidad / Terreno',
        'App\Models\Parentesco' => 'Parentesco',
        'App\Models\ProjectType' => 'Tipo de Proyecto',
        'App\Models\ProjectTypeHasTypology' => 'Tipo de Proyecto / Tipología',
        'App\Models\Stage' => 'Etapa / Estado de Flujo',
        'App\Models\Typology' => 'Tipología',
        'App\Models\Medium' => 'Archivo Multimedia',
        'App\Models\ProjectOld' => 'Proyecto Histórico / Anterior',
    ];

    /**
     * Mapeo de eventos a etiquetas y clases CSS
     */
    public static $eventLabels = [
        'created' => ['label' => 'Creación', 'badge' => 'badge-success'],
        'updated' => ['label' => 'Modificación', 'badge' => 'badge-warning text-dark'],
        'deleted' => ['label' => 'Eliminación', 'badge' => 'badge-danger'],
        'restored' => ['label' => 'Restauración', 'badge' => 'badge-info'],
    ];

    /**
     * Obtener el nombre legible de un modelo
     */
    public static function getModelName($class)
    {
        return self::$modelLabels[$class] ?? class_basename($class);
    }

    /**
     * Formatear información del usuario autor
     */
    public static function getUserData($audit)
    {
        $user = $audit->user;
        $isMuvh = ($audit->user_type === 'App\Models\AdminUser');

        if (!$user && $audit->user_id) {
            if ($isMuvh) {
                $user = \App\Models\AdminUser::withTrashed()->find($audit->user_id);
            } else {
                $user = \App\Models\User::withTrashed()->find($audit->user_id);
            }
        }

        if (!$user) {
            return [
                'name' => 'Sistema / No Registrado',
                'role' => 'Sistema',
                'badge' => 'badge-secondary',
                'email' => '-',
                'id' => $audit->user_id,
            ];
        }

        $name = '';
        if (!empty($user->first_name) || !empty($user->last_name)) {
            $name = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
        }
        if (empty($name)) {
            $name = $user->name ?? $user->email ?? ('ID #' . $user->id);
        }

        return [
            'name' => $name,
            'role' => $isMuvh ? 'MUVH' : 'SAT',
            'badge' => $isMuvh ? 'badge-primary' : 'badge-info',
            'email' => $user->email ?? '-',
            'id' => $user->id,
        ];
    }

    /**
     * Verificar que el usuario tenga acceso a la auditoría (DGTI o Superadmin)
     */
    protected function checkAccess()
    {
        $user = auth()->user();
        $dependencyId = optional($user->rol_app)->dependency_id;

        // Solo DGTI (dependencia 7) o Superadmin sin restricción de dependencia pueden acceder
        if ($dependencyId && $dependencyId != 7) {
            abort(403, 'No tiene autorización para acceder a la auditoría del sistema.');
        }
    }

    /**
     * Listado con filtros y paginación
     */
    public function index(Request $request)
    {
        $this->checkAccess();

        $query = Audit::with('user');

        // Filtro por Modelo
        if ($request->filled('auditable_type')) {
            $query->where('auditable_type', $request->input('auditable_type'));
        }

        // Filtro por Evento (created, updated, deleted, restored)
        if ($request->filled('event')) {
            $query->where('event', $request->input('event'));
        }

        // Filtro por Tipo de Usuario (AdminUser / User)
        if ($request->filled('user_type')) {
            $query->where('user_type', $request->input('user_type'));
        }

        // Filtro por ID de registro auditado
        if ($request->filled('auditable_id')) {
            $query->where('auditable_id', $request->input('auditable_id'));
        }

        // Búsqueda general (ID de auditoría, ID de entidad o IP)
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                if (is_numeric($search)) {
                    $q->where('id', $search)
                      ->orWhere('auditable_id', $search)
                      ->orWhere('user_id', $search);
                } else {
                    $q->where('ip_address', 'like', "%{$search}%")
                      ->orWhere('url', 'like', "%{$search}%");
                }
            });
        }

        // Filtro por Fechas
        if ($request->filled('from_date')) {
            $query->where('created_at', '>=', Carbon::parse($request->input('from_date'))->startOfDay());
        }
        if ($request->filled('to_date')) {
            $query->where('created_at', '<=', Carbon::parse($request->input('to_date'))->endOfDay());
        }

        $perPage = (int) $request->input('per_page', 25);
        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 25;
        }

        $audits = $query->orderBy('id', 'desc')->paginate($perPage)->appends($request->query());

        // Modelos disponibles para el selector
        $availableModels = self::$modelLabels;

        return view('admin.audit.index', [
            'audits' => $audits,
            'modelLabels' => self::$modelLabels,
            'eventLabels' => self::$eventLabels,
            'filters' => $request->all(),
            'perPage' => $perPage,
        ]);
    }

    /**
     * Detalle de un registro de auditoría
     */
    public function show($id, Request $request)
    {
        $this->checkAccess();

        $audit = Audit::with('user')->findOrFail($id);
        $userData = self::getUserData($audit);
        $modelName = self::getModelName($audit->auditable_type);
        $eventInfo = self::$eventLabels[$audit->event] ?? ['label' => ucfirst($audit->event), 'badge' => 'badge-secondary'];

        // Construir comparación detallada de cambios
        $changes = [];
        $oldValues = $audit->old_values ?? [];
        $newValues = $audit->new_values ?? [];
        $allKeys = array_unique(array_merge(array_keys($oldValues), array_keys($newValues)));

        foreach ($allKeys as $key) {
            $old = $oldValues[$key] ?? null;
            $new = $newValues[$key] ?? null;
            $isModified = ($old !== $new);

            $changes[] = [
                'field' => $key,
                'old' => is_array($old) ? json_encode($old, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : (string) $old,
                'new' => is_array($new) ? json_encode($new, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : (string) $new,
                'modified' => $isModified,
            ];
        }

        if ($request->ajax()) {
            return response()->json([
                'id' => $audit->id,
                'event' => $eventInfo['label'],
                'event_badge' => $eventInfo['badge'],
                'user' => $userData,
                'model' => $modelName,
                'model_class' => $audit->auditable_type,
                'auditable_id' => $audit->auditable_id,
                'ip_address' => $audit->ip_address,
                'url' => $audit->url,
                'user_agent' => $audit->user_agent,
                'created_at' => $audit->created_at ? $audit->created_at->format('d/m/Y H:i:s') : '-',
                'changes' => $changes,
            ]);
        }

        return view('admin.audit.show', compact('audit', 'userData', 'modelName', 'eventInfo', 'changes'));
    }
}
