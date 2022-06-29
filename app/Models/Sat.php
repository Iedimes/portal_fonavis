<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sat extends Model
{
    //
    protected $table = 'SHMNUC';
    protected $primaryKey = 'NucCod';
    //protected $keyType = 'string';
    //public $timestamps = false;
    protected $connection = 'sqlsrvsecond';
    public $incrementing = false;
    //protected $fillable = ['NucNomSat'];

    public function setSatId($value)
    {
        $this->attributes['NucCod'] = trim($value);
    }


}
