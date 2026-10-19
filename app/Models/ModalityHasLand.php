<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class ModalityHasLand extends Model implements AuditableContract
{
    use AuditableTrait;

    protected $fillable = [
        'modality_id',
        'land_id',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $appends = ['resource_url'];
    protected $with = ['modality','land'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/modality-has-lands/'.$this->getKey());
    }

    public function modality() {
        return $this->belongsTo('App\Models\Modality');
    }

    public function land() {
        return $this->belongsTo('App\Models\Land');
    }
}
