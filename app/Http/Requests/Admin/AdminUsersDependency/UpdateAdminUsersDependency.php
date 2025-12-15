<?php

namespace App\Http\Requests\Admin\AdminUsersDependency;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateAdminUsersDependency extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('admin.admin-users-dependency.edit', $this->adminUsersDependency);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'admin_user_id' => ['sometimes', 'integer'],
            'dependency_id' => ['sometimes', 'integer'],
        ];
    }

    /**
     * Modify input data
     *
     * @return array
     */
    public function getSanitized(): array
    {
        return $this->validated();
    }

    public function getAdminUserId()
    {
        return $this->get('admin_user_id');
    }

    public function getDependencyId()
    {
        return $this->get('dependency_id');
    }
}
