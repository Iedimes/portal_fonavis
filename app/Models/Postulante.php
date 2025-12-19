<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Postulante extends Model implements AuditableContract
{
    use AuditableTrait;
    use SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'cedula',
        'marital_status',
        'nacionalidad',
        'gender',
        'birthdate',
        'localidad',
        'asentamiento',
        'ingreso',
        'address',
        'grupo',
        'phone',
        'mobile',
        'nexp',
        'hijo_sosten',
        'otra_persona_a_cargo',
        'composicion_del_grupo',
        'documentos_presentados',
        'discapacidad',
        'tercera_edad',
        'ingreso_familiar',
        'cantidad_hijos',
        'documentos_faltantes',
        'observacion_de_consideracion',
        'nivel',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'birthdate', // Asegúrate de que 'birthdate' sea tratado como una fecha
    ];

    protected $appends = ['resource_url'];
    protected $with = ['getProjectHasPostulante', 'getBeneficiaryRelation'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/postulantes/' . $this->getKey());
    }

    public function getProjectHasPostulante()
    {
        return $this->hasOne('App\Models\ProjectHasPostulantes', 'postulante_id', 'id');
    }

    public function getBeneficiaryRelation()
    {
        return $this->hasOne('App\Models\PostulanteHasBeneficiary', 'miembro_id', 'id');
    }

    public function getTitularPostulante()
    {
        // Para miembros/cónyuges: obtener el postulante titular
        return $this->hasManyThrough(
            'App\Models\Postulante',
            'App\Models\PostulanteHasBeneficiary',
            'miembro_id',  // Foreign key en beneficiary table referencing this model
            'id',          // Foreign key en postulante table
            'id',          // Local key en este model
            'postulante_id' // Key en beneficiary table que referencia postulante titular
        );
    }

    /**
     * Determina el tipo de postulante: Titular, Miembro o Sin Clasificar
     */
    public function getPostulanteTypeAttribute()
    {
        if ($this->getProjectHasPostulante) {
            return 'Titular';
        } elseif ($this->getBeneficiaryRelation) {
            return 'Miembro';
        }
        return 'Sin Clasificar';
    }

    /**
     * Retorna el nombre del proyecto si es titular
     */
    public function getProjectNameAttribute()
    {
        return $this->getProjectHasPostulante?->project?->name ?? '-';
    }

    /**
     * Retorna el nombre completo del titular vinculado (para cónyuges/miembros)
     */
    public function getLinkedTitularAttribute()
    {
        if ($this->getBeneficiaryRelation) {
            $titular = $this->getTitularPostulante()->first();
            if ($titular) {
                return $titular->first_name . ' ' . $titular->last_name;
            }
        }
        return '-';
    }

    /**
     * Mutador para asegurar que la fecha de nacimiento siempre se guarde en formato Y-m-d.
     * Esto elimina la hora y milisegundos que pueden venir de la API o del formulario.
     */
    public function setBirthdateAttribute($value)
    {
        $this->attributes['birthdate'] = $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : null;
    }

    /**
     * Scope para eager loading de relaciones necesarias
     */
    public function scopeWithRelationsOptimized($query)
    {
        return $query->with([
            'getProjectHasPostulante' => function ($q) {
                $q->select('postulante_id', 'project_id');
            },
            'getProjectHasPostulante.project' => function ($q) {
                $q->select('id', 'name', 'sat_id');
            },
            'getBeneficiaryRelation' => function ($q) {
                $q->select('miembro_id', 'postulante_id', 'parentesco_id');
            },
            'getBeneficiaryRelation.getParentesco' => function ($q) {
                $q->select('id', 'name');
            },
            'getTitularPostulante' => function ($q) {
                $q->select('postulantes.id', 'postulantes.first_name', 'postulantes.last_name', 'postulantes.cedula');
            }
        ]);
    }
}
