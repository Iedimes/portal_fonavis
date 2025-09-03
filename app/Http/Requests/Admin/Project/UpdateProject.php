<?php

namespace App\Http\Requests\Admin\Project;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateProject extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.project.edit', $this->project);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'phone' => ['required', 'string'],
            'sat_id' => ['required'],          // puede ser string o array
            'state_id' => ['required'],
            'city_id' => ['required'],
            'modalidad_id' => ['required'],    // puede ser string o array
            'leader_name' => ['nullable', 'string'],
            'localidad' => ['nullable', 'string'],
            'land_id' => ['required'],         // puede ser string o array
            'typology_id' => ['required'],     // puede ser string o array
            'action' => ['nullable', 'string'],
            'expsocial' => ['nullable', 'string'],
            'exptecnico' => ['nullable', 'string'],
            'res_nro' => ['required'],
            'coordenadax' => ['nullable', 'string'],
            'coordenaday' => ['nullable', 'string'],
            'ubicacion' => ['nullable', 'string'],
        ];
    }

    /**
    * Modify input data
    *
    * @return array
    */
    public function getSanitized(): array
    {
        $sanitized = $this->validated();

        //Add your code for manipulation with request data here

        return $sanitized;
    }

    public function getSatId()
    {
        $sat = $this->get('sat_id');
        return is_array($sat) ? ($sat['NucCod'] ?? null) : $sat;
    }

    public function getModalidadId()
    {
        $modalidad = $this->get('modalidad_id');
        return is_array($modalidad) ? ($modalidad['id'] ?? null) : $modalidad;
    }

    public function getLandId()
    {
        $land = $this->get('land_id');
        return is_array($land) ? ($land['id'] ?? null) : $land;
    }

    public function getTypologyId()
    {
        $typology = $this->get('typology_id');
        return is_array($typology) ? ($typology['id'] ?? null) : $typology;
    }


    public function getStateId()
    {
        $state = $this->get('state_id');
        return is_array($state) ? ($state['id'] ?? null) : $state;
    }

    public function getCityId()
    {
        $city = $this->get('city_id');
        return is_array($city) ? ($city['id'] ?? null) : $city;
    }
}
