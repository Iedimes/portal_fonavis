<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IVMSOL2 extends Model
{
    protected $table = 'IVMSOL2';
    protected $connection = 'sqlsrvsecond';
    public $incrementing = false;
    protected $primaryKey = 'SolPerCod';
    public $timestamps = false;

    protected $fillable = [
        'SolPerCod',
        'GfsCod',
        'GfsEdad',
        'ParCod',
        'GfsDis',
        'GfsImpSue',
        'GfsImpApo',
        'GfsUsuCod',
        'GfsFecAlta',
        'GfsPEC',
    ];
}
