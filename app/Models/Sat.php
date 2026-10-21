<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CleansUtf8Encoding;

class Sat extends Model
{
    use CleansUtf8Encoding;

    protected $table = 'SHMNUC';
    protected $primaryKey = 'NucCod';
    protected $connection = 'sqlsrvsecond';
    public $incrementing = false;

    // Accessor para limpiar los espacios de NucCod
    public function getNucCodAttribute($value)
    {
        return trim($value);
    }

    public function getNucNomSatAttribute($value)
    {
        return static::fixUtf8($value);
    }
}
