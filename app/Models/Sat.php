<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sat extends Model
{
    protected $table = 'SHMNUC';
    protected $primaryKey = 'NucCod';
    protected $connection = 'sqlsrvsecond';
    public $incrementing = false;

    // Accessor para limpiar los espacios de NucCod
    public function getNucCodAttribute($value)
    {
        return trim($value);
    }
}
