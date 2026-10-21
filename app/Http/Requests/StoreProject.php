<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProject extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        if ($this->has('ubicacion') && is_string($this->ubicacion)) {
            $this->merge([
                'ubicacion' => mb_substr(trim($this->ubicacion), 0, 25),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'phone' => 'required',
            //'households' => 'required',
            'state_id' => 'required',
            'modalidad_id' => 'required',
            'sat_id' => 'required',
            'land_id' => 'required',
            'city_id' => 'required',
            'typology_id' => 'required',
            'leader_name' => 'required',
            'localidad' => 'required',
            'res_nro' => 'required',
            //'finca_nro' => 'required',
            'fechares' => 'required',
            'coordenadax' => 'required',
            'coordenaday' => 'required',
            'ubicacion' => ['required', 'string', 'max:25'],


            //'body' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El campo Nombre es Requerido',
            'phone.required' => 'El campo Telefono es Requerido',
            //'households.required' => 'El campo Camtidad de Viviendas es Requerido',
            'state_id.required' => 'El campo Departamento es Requerido',
            'modalidad_id.required'  => 'El campo Modalidad es Requerido',
            'sat_id.required' => 'El campo Sat es Requerido',
            'land_id.required'  => 'El campo Tipo Terreno es Requerido',
            'city_id.required'  => 'El campo Ciudad es Requerido',
            'leader_name.required'  => 'El campo Nombre del Lider es Requerido',
            'localidad.required'  => 'El campo Localidad es Requerido',
            'res_nro.required'  => 'El campo Resolucion Nro es Requerido',
            //'finca_nro.required'  => 'El campo  Fecha Resolucion es Requerido',
            'fechares.required'  => 'El campo  Fecha Resolucion es Requerido',
            'coordenadax.required'  => 'El campo Coordenada X es Requerido',
            'coordenaday.required'  => 'El campo Coordenada Y es Requerido',
            'ubicacion.required' => 'El campo Ubicación es Requerido',
            'ubicacion.max' => 'El campo Ubicación no debe superar los 25 caracteres',
        ];
    }


    public function getSanitized(): array
    {
        $sanitized = $this->validated();

        if (isset($sanitized['ubicacion'])) {
            $sanitized['ubicacion'] = mb_substr(trim($sanitized['ubicacion']), 0, 25);
        }

        return $sanitized;
    }
}
