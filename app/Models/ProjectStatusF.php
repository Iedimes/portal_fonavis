<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Brackets\Media\HasMedia\ProcessMediaTrait;
use Brackets\Media\HasMedia\AutoProcessMediaTrait;
use Brackets\Media\HasMedia\HasMediaCollectionsTrait;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\HasMedia;
use Brackets\Media\HasMedia\HasMediaThumbsTrait;

class ProjectStatusF extends Model implements HasMedia
{
    use ProcessMediaTrait;
    use AutoProcessMediaTrait;
    use HasMediaCollectionsTrait;
    use HasMediaThumbsTrait;

    protected $table = 'project_status';

    protected $fillable = ['project_id','stage_id','user_id','record'];
    protected $with = ['imagen', 'getStage']; // Carga relaciones automáticamente

    public function imagen()
    {
        return $this->hasMany('App\Models\Medium', 'model_id', 'id');
    }

    public function getStage() {
        return $this->hasOne('App\Models\Stage','id','stage_id');
    }

    public function getUser() {
        return $this->hasOne('App\Models\AdminUser','id','user_id');
    }

    // function registerMediaCollections(): void
    // {
    //     $this->addMediaCollection('gallery')
    //         ->maxFilesize(1024 * 1024 * 30)
    //         ->maxNumberOfFiles(5);
    // }
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('gallery')
            // ->accepts('image/*')
            ->maxFilesize(30) // <- CORRECTO: en MB
            ->maxNumberOfFiles(5);
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->autoRegisterThumb200();
    }
}
