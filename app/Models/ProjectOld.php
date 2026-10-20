<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class ProjectOld extends Model implements AuditableContract
{
    use SoftDeletes;
    use AuditableTrait;

    protected $fillable = [
        'project_id',
        'name',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/project-olds/'.$this->getKey());
    }
}
