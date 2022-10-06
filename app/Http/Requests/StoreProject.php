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

        ];
    }


    public function getSanitized(): array
    {
        $sanitized = $this->validated();

        //Add your code for manipulation with request data here

        return $sanitized;
    }


}
