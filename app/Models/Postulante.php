<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Postulante extends Model implements AuditableContract
{
    use AuditableTrait;
    use SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'cedula',
        'marital_status',
        'nacionalidad',
        'gender',
        'birthdate',
        'localidad',
        'asentamiento',
        'ingreso',
        'address',
        'grupo',
        'phone',
        'mobile',
        'nexp',
        'hijo_sosten',
        'otra_persona_a_cargo',
        'composicion_del_grupo',
        'documentos_presentados',
        'discapacidad',
        'tercera_edad',
        'ingreso_familiar',
        'cantidad_hijos',
        'documentos_faltantes',
        'observacion_de_consideracion',
        'nivel',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'birthdate', // Asegúrate de que 'birthdate' sea tratado como una fecha
    ];

    protected $appends = ['resource_url'];
    protected $with = ['getProjectHasPostulante'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/postulantes/' . $this->getKey());
    }

    public function getProjectHasPostulante()
    {
        return $this->hasOne('App\Models\ProjectHasPostulantes', 'postulante_id', 'id');
    }
}
