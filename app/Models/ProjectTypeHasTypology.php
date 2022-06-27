<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectTypeHasTypology extends Model
{
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
