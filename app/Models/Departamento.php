<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $table = 'BAMDPT';

    protected $primaryKey = 'DptoId';

    protected $connection = 'sqlsrvsecond';

    // protected $appends = ['resource_url'];
    // protected $with = ['localidad'];


    // public function localidad()
    // {
    //     return $this->hasOne(Distrito::class, 'CiuDptoID', 'DptoId');
    // }
}


