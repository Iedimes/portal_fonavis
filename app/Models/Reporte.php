<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reporte extends Model
{
    protected $table = 'reporte';

    protected $fillable = [
        'inicio',
        'fin',
        'sat_id',
        'state_id',
        'city_id',
        'modalidad_id',
        'stage_id',
    
    ];
    
    
    protected $dates = [
        'inicio',
        'fin',
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/reportes/'.$this->getKey());
    }
}
