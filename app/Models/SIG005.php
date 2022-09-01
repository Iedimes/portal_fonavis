<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SIG005 extends Model
{
    protected $table = 'SIG005';

    protected $connection = 'sqlsrvsecond';

    public function tiposol() {
        return $this->hasOne('App\SIG0001','TexCod','TexCod');
    }

    //protected $primaryKey = 'SEOBId';

    /*public function distrito() {
        return $this->hasOne('App\Distrito','CiuId','CiuId');
    }

    public function departamento() {
        return $this->hasOne('App\Departamento','DptoId','DptoId');
    }*/
}
