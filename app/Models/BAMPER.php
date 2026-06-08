<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BAMPER extends Model
{
    protected $table = 'BAMPER';
    protected $connection = 'sqlsrvsecond';
    public $incrementing = false;
    protected $primaryKey = 'PerCod';
    public $timestamps = false;

    protected $fillable = [
        'PerCod',
        'PerNom',
        'PerApePri',
        'PerNomPri',
        'PerApeSeg',
        'PerNomSeg',
        'PerTpDoc',
        'ProCod',
        'ActCod',
        'PerNac',
        'DptoId',
        'CiuId',
        'PerRelPar',
        'PerEstCiv',
        'PerUser',
        'PerSexo',
        'PerFchNac',
        'PerFUM',
        'PerDomic',
        'PerTel1',
        'PerTel2',
    ];
}
