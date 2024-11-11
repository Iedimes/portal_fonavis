<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Postulante extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
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

    ];


    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',

    ];

    protected $appends = ['resource_url'];
    protected $with = ['getProjectHasPostulante'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/postulantes/'.$this->getKey());
    }

    public function getProjectHasPostulante() {
        return $this->hasOne('App\Models\ProjectHasPostulantes','postulante_id', 'id');
    }


}
