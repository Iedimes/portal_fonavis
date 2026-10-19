<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class PostulanteHasBeneficiary extends Model implements AuditableContract
{
    use SoftDeletes;
    use AuditableTrait;

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
