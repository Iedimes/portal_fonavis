<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Illuminate\Support\Facades\DB;

class ProjectHasPostulantes extends Model implements AuditableContract
{
    use SoftDeletes;
    use AuditableTrait;

    protected $fillable = [
        'postulante_id',
        'project_id',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $with = ['project'];

    /* ************************ RELACIONES ************************* */

    public function getPostulante()
    {
        return $this->hasOne('App\Models\Postulante', 'id', 'postulante_id');
    }

    public function getMembers()
    {
        return $this->hasMany('App\Models\PostulanteHasBeneficiary', 'postulante_id', 'postulante_id');
    }

    public function project()
    {
        return $this->hasOne('App\Models\Project', 'id', 'project_id');
    }

    /* ************************ MÉTODOS ORIGINALES (mantener por compatibilidad) ************************* */

    public static function getNivel($id)
    {
        $postulante = Postulante::find($id);
        if (!$postulante) {
            return null;
        }

        $conyuge = PostulanteHasBeneficiary::where('postulante_id', $id)
            ->whereIn('parentesco_id', [1, 8])
            ->first();
        $ingresoConyuge = 0;
        if ($conyuge) {
            $miembroPostulante = Postulante::find($conyuge->miembro_id);
            $ingresoConyuge = $miembroPostulante->ingreso ?? 0;
        }

        $grupo = $postulante->ingreso + $ingresoConyuge;

        return self::calcularNivel($grupo);
    }

    public static function getIngreso($id)
    {
        $postulante = Postulante::find($id);
        if (!$postulante) {
            return 0;
        }

        $conyuge = PostulanteHasBeneficiary::where('postulante_id', $id)
            ->whereIn('parentesco_id', [1, 8])
            ->first();
        $ingresoConyuge = 0;
        if ($conyuge) {
            $miembroPostulante = Postulante::find($conyuge->miembro_id);
            $ingresoConyuge = $miembroPostulante->ingreso ?? 0;
        }

        return $postulante->ingreso + $ingresoConyuge;
    }

    /* ************************ NUEVOS MÉTODOS OPTIMIZADOS ************************* */

    /**
     * Obtiene otros ingresos (miembros no cónyuges) para múltiples postulantes.
     * Si 'otros_ingresos' en postulantes es null, calcula la suma de los miembros con parentesco != 1 y != 8
     * y guarda el resultado en la base de datos PostgreSQL.
     *
     * @param array $postulanteIds IDs de postulantes
     * @return array ['postulante_id' => otros_ingresos]
     */
    public static function getOtrosIngresosBatch($postulanteIds)
    {
        if (empty($postulanteIds)) {
            return [];
        }

        $postulantes = Postulante::whereIn('id', $postulanteIds)
            ->get()
            ->keyBy('id');

        // Query: Miembros que NO son cónyuges (parentesco_id != 1 y != 8)
        $otrosMiembros = PostulanteHasBeneficiary::whereIn('postulante_id', $postulanteIds)
            ->whereNotIn('parentesco_id', [1, 8])
            ->select('postulante_id', 'miembro_id')
            ->get();

        $miembroIds = $otrosMiembros->pluck('miembro_id')->unique()->filter();
        $miembrosIngresos = Postulante::whereIn('id', $miembroIds)
            ->pluck('ingreso', 'id')
            ->toArray();

        $otrosIngresosCalculados = [];
        foreach ($otrosMiembros as $om) {
            $ing = $miembrosIngresos[$om->miembro_id] ?? 0;
            if (!isset($otrosIngresosCalculados[$om->postulante_id])) {
                $otrosIngresosCalculados[$om->postulante_id] = 0;
            }
            $otrosIngresosCalculados[$om->postulante_id] += $ing;
        }

        $resultado = [];
        foreach ($postulanteIds as $postulanteId) {
            $postulante = $postulantes[$postulanteId] ?? null;

            if ($postulante && $postulante->otros_ingresos !== null) {
                // Si el usuario editó manualmente este campo, usar el valor guardado
                $resultado[$postulanteId] = (float) $postulante->otros_ingresos;
            } else {
                // Si es NULL en DB, calcular dinámicamente la suma de miembros no cónyuges
                $resultado[$postulanteId] = $otrosIngresosCalculados[$postulanteId] ?? 0;
            }
        }

        return $resultado;
    }

    /**
     * Calcula ingresos para múltiples postulantes en una sola operación
     *
     * @param array $postulanteIds IDs de postulantes
     * @return array ['postulante_id' => ingreso_total]
     */
    public static function getIngresosBatch($postulanteIds)
    {
        if (empty($postulanteIds)) {
            return [];
        }

        // Query 1: Traer ingresos de postulantes principales
        $postulantesIngresos = Postulante::whereIn('id', $postulanteIds)
            ->pluck('ingreso', 'id')
            ->toArray();

        // Query 2: Traer ingresos de cónyuges (parentesco 1=esposo/a, 8=concubino/a)
        $conyuges = PostulanteHasBeneficiary::whereIn('postulante_id', $postulanteIds)
            ->whereIn('parentesco_id', [1, 8])
            ->select('postulante_id', 'miembro_id')
            ->get();

        $conyugeIds = $conyuges->pluck('miembro_id')->unique()->filter();
        $conyugesIngresos = Postulante::whereIn('id', $conyugeIds)
            ->pluck('ingreso', 'id')
            ->toArray();

        $conyugeMap = $conyuges->keyBy('postulante_id');

        // Query 3: Traer otros ingresos (miembros no cónyuges o valor editado)
        $otrosIngresos = self::getOtrosIngresosBatch($postulanteIds);

        // Calcular ingreso total por postulante (titular + conyuge + otros_ingresos)
        $resultado = [];
        foreach ($postulanteIds as $postulanteId) {
            $ingresoPostulante = $postulantesIngresos[$postulanteId] ?? 0;
            $ingresoConyuge = 0;

            if (isset($conyugeMap[$postulanteId])) {
                $ingresoConyuge = $conyugesIngresos[$conyugeMap[$postulanteId]->miembro_id] ?? 0;
            }

            $ingresoOtros = $otrosIngresos[$postulanteId] ?? 0;

            $resultado[$postulanteId] = $ingresoPostulante + $ingresoConyuge + $ingresoOtros;
        }

        return $resultado;
    }

    /**
     * Calcula niveles para múltiples postulantes
     *
     * @param array $postulanteIds IDs de postulantes
     * @return array ['postulante_id' => nivel]
     */
    public static function getNivelesBatch($postulanteIds)
    {
        $ingresos = self::getIngresosBatch($postulanteIds);

        $niveles = [];
        foreach ($ingresos as $postulanteId => $ingreso) {
            $niveles[$postulanteId] = self::calcularNivel($ingreso);
        }

        return $niveles;
    }

    /**
     * Calcula el nivel según el ingreso
     *
     * @param float $ingreso
     * @return string|null
     */
    public static function calcularNivel($ingreso)
    {
        if ($ingreso <= 2798309) return '4';
        if ($ingreso <= 5316789) return '3';
        if ($ingreso <= 18077086) return '2';
        if ($ingreso <= 90385435) return '1';

        return null;
    }
}
