<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class PostulanteHasBeneficiary extends Model
{
    use SoftDeletes; // 👈 Agregar esto

    protected $table = 'postulante_has_beneficiaries';

    protected $fillable = [
        'postulante_id',
        'miembro_id',
        'parentesco_id',
    ];

    protected $dates = ['deleted_at']; // 👈 opcional, pero recomendable

    public function getPostulante() {
        return $this->hasOne('App\Models\Postulante','id','miembro_id');
    }

    public function getParentesco() {
        return $this->hasOne('App\Models\Parentesco','id','parentesco_id');
    }
}
