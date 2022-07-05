<?php

namespace App\Http\Requests\Admin\Assignment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateAssignment extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.assignment.edit', $this->assignment);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'document_id' => ['nullable'],
            'category_id' => ['nullable'],
            'project_type_id' => ['nullable'],
            'stage_id' => ['nullable'],

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


    public function getDocumentId()
    {
        return $this->get('document')['id'];
    }
    public function getCategoryId()
    {
        return $this->get('category')['id'];
    }
    public function getPtId()
    {
        return $this->get('project_type')['id'];
    }
    public function getStageId()
    {
        return $this->get('stage')['id'];
    }

}
