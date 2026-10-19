<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class ProjectTypeHasTypology extends Model implements AuditableContract
{
    use AuditableTrait;

    protected $fillable = [
        'project_type_id',
        'typology_id',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $appends = ['resource_url'];
    protected $with = ['projectType','typology'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/project-type-has-typologies/'.$this->getKey());
    }

    public function projectType() {
        return $this->belongsTo('App\Models\ProjectType');
    }

    public function typology() {
        return $this->belongsTo('App\Models\Typology');
    }
}
