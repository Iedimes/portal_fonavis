<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class ProjectHasExpediente extends Model implements AuditableContract
{
    use AuditableTrait;
    use SoftDeletes;

    protected $fillable = [
        'project_id',
        'exp',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $appends = ['resource_url'];
    protected $with = ['proyecto', 'expediente'];

    /* ************************ RELACIONES ************************* */

    public function proyecto()
    {
        return $this->hasOne('App\Models\Project', 'id', 'project_id');
    }

    public function expediente()
    {
        return $this->hasOne('App\Models\SIG005', 'NroExp', 'exp');
    }



    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/project-has-expedientes/'.$this->getKey());
    }
}
