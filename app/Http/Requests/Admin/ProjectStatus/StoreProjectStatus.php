<?php

namespace App\Http\Requests\Admin\ProjectStatus;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

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
            'dependencia' => ['required', 'integer'], // <-- agregar

        ];

        $stageId = $this->getStageId(); // obtener el ID del stage
        $dependencia = $this->get('dependencia'); // obtener la dependencia del request

         // depuración
    // dd($stageId, $dependencia, gettype($stageId), gettype($dependencia));

        // Validación para requerir 'gallery' según condiciones
        if ($stageId === 3 || $stageId === 5 || ($stageId === 13 && $dependencia === 4)) {
            $rules['gallery'] = ['required'];
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

        // Aquí puedes manipular los datos antes de retornarlos si es necesario

        return $sanitized;
    }

    /**
     * Obtener el ID del stage desde el request
     *
     * @return int|null
     */
    public function getStageId()
    {
        $stage = $this->get('stage');
        return $stage['id'] ?? null;
    }
}
