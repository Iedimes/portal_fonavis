<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostulanteHasDiscapacidad extends Model
{
    protected $table = 'postulante_has_discapacidad';

    protected $fillable = [
        'discapacidad_id',
        'postulante_id',
    ];

    // public $timestamps = false; // si no tienes columnas created_at/updated_at
}
