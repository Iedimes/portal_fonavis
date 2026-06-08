<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IVMSOL extends Model
{
    protected $table = 'IVMSOL';
    protected $connection = 'sqlsrvsecond';
    public $incrementing = false;
    protected $primaryKey = 'SolPerCod';
    public $timestamps = false;

    protected $fillable = [
        'SolPerCod',
        'SolSer',
        'SolNro',
        'SolFch',
        'SolTieUni',
        'SolAuto',
        'SolEquipo',
        'SolMaquin',
        'SolAnimal',
        'SolOtros',
        'SolTipo',
        'SolInscri',
        'SolComent',
        'SolPerCge',
        'SolHabViv',
        'SolFum',
        'SolEtapa',
        'SolReFecAd',
        'SolReNroAd',
        'SolCodObra',
    ];
}
