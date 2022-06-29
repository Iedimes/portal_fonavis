<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Project_tipologies extends Model
{


    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'project_type_has_typologies';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = ['project_type_id','typology_id'];
    // protected $hidden = [];
    // protected $dates = [];

    public function getDateFormat()
    {
        return 'Y-d-m H:i:s.v';
    }

    public function tipo() {
        return $this->hasOne('App\Models\Project_type','id','project_type_id');
    }

    public function tipologia() {
        return $this->hasOne('App\Models\Typology','id','typology_id');
    }

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
