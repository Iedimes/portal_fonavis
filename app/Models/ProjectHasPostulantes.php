<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class ProjectHasPostulantes extends Model implements AuditableContract
{
    use SoftDeletes;
    use AuditableTrait;

    protected $fillable = [
        'postulante_id',
        'project_id',
        // Agrega otros campos que necesites aquí
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

    /* ************************ MÉTODOS ESTÁTICOS ************************* */

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

        if ($grupo <= 2798309) return '4';
        if ($grupo <= 5316789) return '3';
        if ($grupo <= 18077086) return '2';
        if ($grupo <= 90385435) return '1';

        return null;
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
}
