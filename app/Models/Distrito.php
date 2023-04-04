<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distrito extends Model
{
    protected $table = 'LOCALIDA';

    protected $primaryKey = 'CiuId';

    //protected $connection = 'pgsql';

    protected $connection = 'sqlsrvsecond';


    //public $timestamps = false;

    //protected $connection = 'sqlsrv';

    //protected $dateFormat = 'Y-m-d H:i:s.v';

    //public $incrementing = false;
}
