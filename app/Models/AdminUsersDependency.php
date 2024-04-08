<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminUsersDependency extends Model
{
    protected $fillable = [
        'admin_user_id',
        'dependency_id',

    ];


    protected $dates = [
        'created_at',
        'updated_at',

    ];

    protected $appends = ['resource_url'];

    protected $with = ['dependencia', 'usuario'];


    public function usuario()
    {
        return $this->hasOne('App\Models\AdminUser', 'id', 'admin_user_id');
    }

    public function dependencia()
    {
        return $this->belongsTo('App\Models\Dependency', 'dependency_id', 'id' );
    }
    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/admin-users-dependencies/'.$this->getKey());
    }
}
