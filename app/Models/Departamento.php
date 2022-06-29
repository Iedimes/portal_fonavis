<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $table = 'BAMDPT';

    protected $primaryKey = 'DptoId';

    protected $connection = 'sqlsrvsecond';
}
