<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class POSSVS1 extends Model
{
    protected $table = 'POSSVS1';
    protected $connection = 'sqlsrvsecond';
    public $timestamps = false;

    protected $fillable = [
        'PsvCod',
        'Psvord',
        'PsvBibNro',
        'PsvExpNro',
        'PsvExpS',
        'PsvTDPos',
        'PsvTDPosM',
        'PsvCedTit',
        'PsvNomTit',
        'PsvTDCge',
        'PsvTDCgeM',
        'PsvCedCge',
        'PsvNomCge',
        'PsvNivel',
        'PsvCanHij',
        'PsvDiscap',
        'PsvTerEdad',
        'PsvSosten',
        'PsvAporte',
        'PsvIfac',
        'PsvDomi',
        'PsvObs',
        'PsvRegCon',
        'PsvUsuIng',
        'PsvUsuMod',
        'PsvFecIng',
        'PsvFecMod',
        'PsvIngTit',
        'PsvIngCge',
        'PsvIngOtr',
        'PsvIngFam',
        'PsvNomSos',
        'PsvCgeFNac',
        'PsvTitFNac',
        'PsvTerreno',
        'PsvObs2',
        'PsvObsSost',
    ];
}
