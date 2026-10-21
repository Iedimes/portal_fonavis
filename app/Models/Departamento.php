<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CleansUtf8Encoding;

class Departamento extends Model
{
    use CleansUtf8Encoding;

    protected $table = 'BAMDPT';

    protected $primaryKey = 'DptoId';

    protected $connection = 'sqlsrvsecond';

    public function getDptoNomAttribute($value)
    {
        return static::fixUtf8($value);
    }

    // protected $appends = ['resource_url'];
    // protected $with = ['localidad'];


    // public function localidad()
    // {
    //     return $this->hasOne(Distrito::class, 'CiuDptoID', 'DptoId');
    // }
}


