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

        $miembros = PostulanteHasBeneficiary::where('postulante_id', $id)->get();
        $total = Postulante::whereIn('id', $miembros->pluck('miembro_id'))->get();
        $ingreso = $total->sum('ingreso');
        $grupo = $ingreso + $postulante->ingreso;

        return self::calcularNivel($grupo);
    }

    public static function getIngreso($id)
    {
        $postulante = Postulante::find($id);
        if (!$postulante) {
            return 0;
        }

        $miembros = PostulanteHasBeneficiary::where('postulante_id', $id)->get();
        $total = Postulante::whereIn('id', $miembros->pluck('miembro_id'))->get();
        $ingreso = $total->sum('ingreso');

        return $ingreso + $postulante->ingreso;
    }

    /* ************************ NUEVOS MÉTODOS OPTIMIZADOS ************************* */

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

        // Query 2: Traer todas las relaciones de beneficiarios
        $miembrosRelaciones = PostulanteHasBeneficiary::whereIn('postulante_id', $postulanteIds)
            ->select('postulante_id', 'miembro_id')
            ->get()
            ->groupBy('postulante_id');

        // Query 3: Traer ingresos de todos los miembros
        $miembrosIds = $miembrosRelaciones->flatten()->pluck('miembro_id')->unique()->filter();
        $miembrosIngresos = Postulante::whereIn('id', $miembrosIds)
            ->pluck('ingreso', 'id')
            ->toArray();

        // Calcular ingreso total por postulante
        $resultado = [];
        foreach ($postulanteIds as $postulanteId) {
            $ingresoPostulante = $postulantesIngresos[$postulanteId] ?? 0;
            $ingresoMiembros = 0;

            if (isset($miembrosRelaciones[$postulanteId])) {
                foreach ($miembrosRelaciones[$postulanteId] as $relacion) {
                    $ingresoMiembros += $miembrosIngresos[$relacion->miembro_id] ?? 0;
                }
            }

            $resultado[$postulanteId] = $ingresoPostulante + $ingresoMiembros;
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
