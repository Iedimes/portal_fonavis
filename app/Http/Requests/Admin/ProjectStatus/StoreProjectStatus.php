<?php

namespace App\Http\Requests\Admin\ProjectStatus;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreProjectStatus extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.project-status.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
{
    $rules = [
        'project_id' => ['required'],
        'stage' => ['required'],
        'user_id' => ['required', 'integer'],
        'record' => ['required', 'string'],
    ];

    $stageId = $this->getStageId(); // Almacenar el valor antes de la condición

    if ($stageId === 3 || $stageId === 5) {
        $rules['gallery'] = ['required'];
    } else {
        //dd('Sale por Else'.$stageId); // Imprimir el valor si no se entra en la condición
    }

    return $rules;
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

    public function getStageId()
     {
         return $this->get('stage')['id'];
     }
}

