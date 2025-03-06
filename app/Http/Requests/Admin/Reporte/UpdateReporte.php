<?php

namespace App\Http\Requests\Admin\Reporte;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateReporte extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.reporte.edit', $this->reporte);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'inicio' => ['sometimes', 'date'],
            'fin' => ['sometimes', 'date'],
            'sat_id' => ['sometimes', 'string'],
            'state_id' => ['sometimes', 'string'],
            'city_id' => ['sometimes', 'string'],
            'modalidad_id' => ['sometimes', 'string'],
            'stage_id' => ['sometimes', 'integer'],
            
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
}
