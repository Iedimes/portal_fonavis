<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class DighObservation extends Model implements AuditableContract
{
    use AuditableTrait;

    protected $fillable = [
        'document_id',
        'project_id',
        'observation',
        'origen',


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
