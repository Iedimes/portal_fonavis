<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Documentsmissing extends Model implements AuditableContract
{
    use SoftDeletes;
    use AuditableTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    /*public function getDateFormat()
    {
        return 'Y-d-m H:i:s.v';
    }*/

    protected $table = 'project_documentationsmissing';
    // protected $primaryKey = 'id';
    //public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = ['project_id', 'document_id', 'title', 'file_path'];
    // protected $hidden = [];
    // protected $dates = [];
    //protected $dateFormat = 'Y-m-d H:i:s.v';
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
