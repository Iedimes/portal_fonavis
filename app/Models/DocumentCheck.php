<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentCheck extends Model
{
    //protected $table="document_checks";
    protected $fillable = [
        'project_id',
        'document_id',

    ];


    protected $dates = [
        'created_at',
        'updated_at',

    ];

    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/document-checks/'.$this->getKey());
    }
}
