<?php

namespace App\Http\Requests\Admin\ModalityHasLand;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateModalityHasLand extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.modality-has-land.edit', $this->modalityHasLand);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'modality' => ['nullable'],
            'land' => ['nullable'],

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

    public function getModalityId()
    {
        return $this->get('modality')['id'];
    }

    public function getLandId()
    {
        return $this->get('land')['id'];
    }
}
