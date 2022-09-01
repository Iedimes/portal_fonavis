<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostulanteHasBeneficiary extends Model
{
    //
    public function getDateFormat()
    {
        return 'Y-d-m H:i:s.v';
    }

    public function getPostulante() {
        return $this->hasOne('App\Models\Postulante','id','miembro_id');
    }

    public function getParentesco() {
        return $this->hasOne('App\Models\Parentesco','id','parentesco_id');
    }
}
