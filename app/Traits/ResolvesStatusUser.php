<?php

namespace App\Traits;

use App\Models\User;
use App\Models\AdminUser;

trait ResolvesStatusUser
{

    /**
     * Resuelve el usuario autor del cambio de estado, diferenciando si es Admin (MUVH) o SAT
     *
     * @param  mixed  $item
     * @param  mixed  $project
     * @param  \Illuminate\Support\Collection|null  $adminUsers
     * @param  \Illuminate\Support\Collection|null  $satUsers
     * @param  \Illuminate\Support\Collection|null  $audits
     * @return array
     */
    public static function resolveStatusUser($item, $project = null, $adminUsers = null, $satUsers = null, $audits = null)
    {
        $userId = $item->user_id;

        if (!$userId) {
            $isSatSend = ($item->record === 'Proyecto Enviado!');
            $satName = ($project && $project->getSat) ? trim($project->getSat->NucNomSat) : '';
            if ($isSatSend && !empty($satName)) {
                return [
                    'name' => $satName,
                    'role' => 'SAT',
                    'display' => $satName . ' (SAT)',
                    'badge' => 'badge-success',
                ];
            }
            return [
                'name' => 'Sistema',
                'role' => 'Sistema',
                'display' => 'Sistema',
                'badge' => 'badge-secondary',
            ];
        }

        $adminUser = $adminUsers ? $adminUsers->get($userId) : AdminUser::withTrashed()->find($userId);
        $satUser = $satUsers ? $satUsers->get($userId) : User::withTrashed()->find($userId);
        $audit = $audits ? $audits->get($item->id) : null;

        // 1. Si existe auditoría registrada, sabemos el modelo exacto con certeza
        if ($audit) {
            if ($audit->user_type === 'App\Models\AdminUser' && $adminUser) {
                $name = trim($adminUser->first_name . ' ' . $adminUser->last_name) ?: $adminUser->email;
                return [
                    'name' => $name,
                    'role' => 'MUVH',
                    'display' => $name . ' (MUVH)',
                    'badge' => 'badge-primary',
                ];
            }
            if ($audit->user_type === 'App\Models\User' && $satUser) {
                $name = $satUser->name ?: $satUser->email;
                return [
                    'name' => $name,
                    'role' => 'SAT',
                    'display' => $name . ' (SAT)',
                    'badge' => 'badge-success',
                ];
            }
        }

        // 2. Solo existe en AdminUser
        if ($adminUser && !$satUser) {
            $name = trim($adminUser->first_name . ' ' . $adminUser->last_name) ?: $adminUser->email;
            return [
                'name' => $name,
                'role' => 'MUVH',
                'display' => $name . ' (MUVH)',
                'badge' => 'badge-primary',
            ];
        }

        // 3. Solo existe en User (SAT)
        if ($satUser && !$adminUser) {
            $name = $satUser->name ?: $satUser->email;
            return [
                'name' => $name,
                'role' => 'SAT',
                'display' => $name . ' (SAT)',
                'badge' => 'badge-success',
            ];
        }

        // 4. Existe en ambos (Colisión de ID entre admin_users y users)
        if ($adminUser && $satUser) {
            // Regla de origen: el SAT únicamente emite estados en las etapas 1, 5, 8, 11 con los mensajes del sistema
            $isSatRecord = in_array($item->stage_id, [1, 5, 8, 11]) && (
                in_array($item->record, [
                    'Proyecto Enviado!',
                    'CON DOCUMENTACION DGJN!',
                    'GRUPO FAMILIAR ENVIADO!',
                    'DOCUMENTACION TECNICA ENVIADA!'
                ]) || empty($item->record)
            );

            if ($isSatRecord) {
                $name = $satUser->name ?: $satUser->email;
                return [
                    'name' => $name,
                    'role' => 'SAT',
                    'display' => $name . ' (SAT)',
                    'badge' => 'badge-success',
                ];
            } else {
                $name = trim($adminUser->first_name . ' ' . $adminUser->last_name) ?: $adminUser->email;
                return [
                    'name' => $name,
                    'role' => 'MUVH',
                    'display' => $name . ' (MUVH)',
                    'badge' => 'badge-primary',
                ];
            }
        }

        // 5. Fallback si el ID no se encontró en ninguna tabla
        $satName = ($project && $project->getSat) ? trim($project->getSat->NucNomSat) : '';
        if ($item->record === 'Proyecto Enviado!' && !empty($satName)) {
            return [
                'name' => $satName,
                'role' => 'SAT',
                'display' => $satName . ' (SAT)',
                'badge' => 'badge-success',
            ];
        }

        return [
            'name' => 'Usuario #' . $userId,
            'role' => 'Desconocido',
            'display' => 'Usuario #' . $userId,
            'badge' => 'badge-secondary',
        ];
    }

    /**
     * Accessor dinámico para nombre_usuario
     */
    public function getNombreUsuarioAttribute()
    {
        if (isset($this->attributes['nombre_usuario'])) {
            return $this->attributes['nombre_usuario'];
        }

        $res = static::resolveStatusUser($this);
        return $res['display'];
    }
}
