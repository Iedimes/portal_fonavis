<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = 'BDIDENT1';
    // protected $dateFormat = 'd-m-Y H:i:s.u';
    public $timestamps = false;

    //protected $connection = 'pgsql';

    // protected $fillable = [
    //     'cedula',
    //     'nombre',
    //     'apellido',
    //     'fecha_nacimiento',
    //     'estado_civil',
    //     'profesion',
    //     'sexo',

    // ];


    // protected $dates = [
    //     'fecha_nacimiento',
    //     'created_at',
    //     'updated_at',

    // ];

    // protected $appends = ['resource_url'];

    // /* ************************ ACCESSOR ************************* */

    // public function getResourceUrlAttribute()
    // {
    //     return url('/admin/personas/'.$this->getKey());
    // }
}
