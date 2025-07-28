<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DighObservation extends Model
{
    protected $fillable = [
        'document_id',
        'project_id',
        'observation',
    
    ];
    
    
    protected $dates = [
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/digh-observations/'.$this->getKey());
    }
}
