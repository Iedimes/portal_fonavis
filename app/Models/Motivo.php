<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Motivo extends Model
{
    protected $fillable = [
        'project_id',
        'motivo',
    
    ];
    
    
    protected $dates = [
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/motivos/'.$this->getKey());
    }
}
