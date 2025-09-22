<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SIG006 extends Model
{
    protected $table = 'SIG006';

    protected $connection = 'sqlsrvsecond';

    protected $fillable = [
        'NroExp',
        'NroExpS',
        'DENroLin',
        'DEFecDis',
        'UsuRcp',
        'UsuCod',
        'DEUnOrHa',
        'DEExpAcc',
        'DEExpEst',
        'DEFecMod',
        'DEFecRec',
        'DERecep',
        'DEUnOrDe',
        'DEFecRcp',
        'DERcpChk',
        'DEFecEnt',
        'DERcpNam',
    ];

    public $timestamps = false; // Deshabilitar marcas de tiempo

    // Definir los casts
    protected $casts = [
        'DEFecDis' => 'datetime:Y-m-d H:i:s', // Formato para DEFecDis
    ];
}
