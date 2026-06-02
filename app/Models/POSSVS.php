<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class POSSVS extends Model
{
    protected $table = 'POSSVS';
    protected $connection = 'sqlsrvsecond';
    public $timestamps = false;

    protected $fillable = [
        'PsvModDes',
        'NucCod',
        'PsvDptoId',
        'PsvCiudId',
    ];
}
