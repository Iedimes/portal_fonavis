<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class DocumentCheck extends Model implements AuditableContract
{
    use AuditableTrait;

    protected $fillable = [
        'project_id',
        'document_id',
        'sheets'

    ];


    protected $dates = [
        'created_at',
        'updated_at',

    ];

    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/document-checks/'.$this->getKey());
    }
}
