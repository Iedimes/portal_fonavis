<?php

namespace App\Http\Requests\Admin\LandHasProjectType;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateLandHasProjectType extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.land-has-project-type.edit', $this->landHasProjectType);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'land_id' => ['nullable'],
            'project_type' => ['nullable'],

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

    public function getPtId()
     {
         return $this->get('project_type')['id'];
     }

     public function getLandId()
     {
         return $this->get('land')['id'];
     }
}
