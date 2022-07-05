<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $fillable = [
        'document_id',
        'category_id',
        'project_type_id',
        'stage_id',

    ];


    protected $dates = [
        'created_at',
        'updated_at',

    ];

    protected $appends = ['resource_url'];
    protected $with = ['projectType','category','document','stage'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/assignments/'.$this->getKey());
    }

    public function projectType() {
        return $this->belongsTo('App\Models\ProjectType');
    }

    public function category() {
        return $this->belongsTo('App\Models\Category');
    }

    public function document() {
        return $this->belongsTo('App\Models\Document');
    }
    public function stage() {
        return $this->belongsTo('App\Models\Stage');
    }

    public function check() {
        return $this->belongsTo(DocumentCheck::class, 'document_id', 'document_id');
    }
}
