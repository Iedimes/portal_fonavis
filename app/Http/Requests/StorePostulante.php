<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePostulante extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        return [
            'cedula' => [
                'required',
                'string',
                Rule::unique('postulantes')->where(function ($query) {
                    return $query->whereNull('deleted_at');
                }),
            ],
            // Agregá aquí las demás validaciones necesarias, por ejemplo:
            // 'first_name' => 'required|string|max:255',
            // 'last_name' => 'required|string|max:255',
        ];
    }

    /**
     * Mensajes personalizados para errores de validación.
     */
    public function messages()
    {
        return [
            'cedula.unique' => 'Ya existe un postulante activo con esta cédula.',
            'cedula.required' => 'El campo cédula es obligatorio.',
        ];
    }
}
