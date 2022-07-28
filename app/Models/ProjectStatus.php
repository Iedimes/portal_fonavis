<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectStatus extends Model
{
    //
    protected $table = 'project_status';

    protected $fillable = ['project_id','stage_id','user_id','record'];

    /*public function getDateFormat()
    {
        return 'Y-d-m H:i:s.v';
    }*/

    public function getStage() {
        return $this->hasOne('App\Models\Stage','id','stage_id');
    }

    public function getUser() {
        return $this->hasOne('App\Models\AdminUser','id','user_id');
    }


}
