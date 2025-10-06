<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostulanteHasBeneficiary extends Model
{
    protected $table = 'postulante_has_beneficiaries';

    // Permitir asignación masiva
    protected $fillable = [
        'postulante_id',
        'miembro_id',
        'parentesco_id',
    ];

    public function getPostulante() {
        return $this->hasOne('App\Models\Postulante','id','miembro_id');
    }

    public function getParentesco() {
        return $this->hasOne('App\Models\Parentesco','id','parentesco_id');
    }
}
