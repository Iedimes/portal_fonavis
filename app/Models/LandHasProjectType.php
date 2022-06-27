<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandHasProjectType extends Model
{
    protected $table = 'land_has_project_type';

    protected $fillable = [
        'land_id',
        'project_type_id',

    ];


    protected $dates = [
        'created_at',
        'updated_at',

    ];

    protected $appends = ['resource_url'];
    protected $with = ['land','projectType'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/land-has-project-types/'.$this->getKey());
    }

    public function land() {
        return $this->belongsTo('App\Models\Land');
    }

    public function projectType() {
        return $this->belongsTo('App\Models\ProjectType');
    }
}
