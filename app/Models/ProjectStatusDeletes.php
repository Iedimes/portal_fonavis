<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class ProjectStatusDeletes extends Model implements AuditableContract
{
    use AuditableTrait;

    protected $table = 'project_status_deletes';

    protected $fillable = ['project_id','stage_id','user_id','record'];

    /*public function getDateFormat()
    {
        return 'Y-d-m H:i:s.v';
    }*/

    // public function getStage() {
    //     return $this->hasOne('App\Models\Stage','id','stage_id');
    // }

    // public function getUser() {
    //     return $this->hasOne('App\Models\AdminUser','id','user_id');
    // }


}
