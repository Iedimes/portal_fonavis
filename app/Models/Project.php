<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Project extends Model implements AuditableContract
{
    use SoftDeletes;           // Eliminación suave
    use AuditableTrait;        // Auditoría compatible

    protected $fillable = [
        'name', 'phone', 'sat_id', 'state_id', 'city_id', 'land_id', 'modalidad_id',
        'localidad', 'leader_name', 'typology_id', 'expsocial', 'exptecnico',
        'action', 'households', 'certificate_pin', 'res_nro', 'finca_nro',
        'fechares', 'coordenadax', 'coordenaday', 'ubicacion'
    ];

    protected $with = ['getState', 'getModality', 'getCity', 'getEstado', 'getSat'];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $appends = ['resource_url'];

    /* ************************ RELACIONES ************************* */

    public function getSat()
    {
        return $this->hasOne('App\Models\Sat', 'NucCod', 'sat_id');
    }

    public function getLand()
    {
        return $this->hasOne('App\Models\Land', 'id', 'land_id');
    }

    public function getprojectType()
    {
        return $this->hasOne('App\Models\LandHasProjectType', 'land_id', 'land_id');
    }

    public function getState()
    {
        return $this->hasOne('App\Models\Departamento', 'DptoId', 'state_id');
    }

    public function getCity()
    {
        return $this->hasOne('App\Models\Distrito', 'CiuId', 'city_id');
    }

    public function getModality()
    {
        return $this->hasOne('App\Models\Modality', 'id', 'modalidad_id');
    }

    public function getTypology()
    {
        return $this->hasOne('App\Models\Typology', 'id', 'typology_id');
    }

    public function getEstado()
    {
        return $this->hasOne('App\Models\ProjectStatus', 'project_id', 'id')->latest('updated_at');
    }

    public function getEstados()
    {
        return $this->hasMany('App\Models\ProjectStatus', 'project_id', 'id');
    }

    /* ************************ ACCESSORS ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/projects/' . $this->getKey());
    }
}
