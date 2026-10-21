<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CleansUtf8Encoding;

class Distrito extends Model
{
    use CleansUtf8Encoding;

    protected $table = 'LOCALIDA';

    protected $primaryKey = 'CiuId';

    protected $connection = 'sqlsrvsecond';

    public function getCiuNomAttribute($value)
    {
        return static::fixUtf8($value);
    }


    //public $timestamps = false;

    //protected $connection = 'sqlsrv';

    //protected $dateFormat = 'Y-m-d H:i:s.v';

    //public $incrementing = false;
}
