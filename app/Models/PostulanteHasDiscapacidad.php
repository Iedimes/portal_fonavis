<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class PostulanteHasDiscapacidad extends Model implements AuditableContract
{
    use AuditableTrait;

    protected $table = 'postulante_has_discapacidad';

    protected $fillable = [
        'discapacidad_id',
        'postulante_id',
    ];

    // public $timestamps = false; // si no tienes columnas created_at/updated_at
}
