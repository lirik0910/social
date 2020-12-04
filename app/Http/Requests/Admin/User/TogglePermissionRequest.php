<?php

namespace App\Http\Requests\Admin\User;

use App\Helpers\AdminPermissionsHelper;
use App\Libraries\GraphQL\AbstractValidation;
use App\Models\User;

class TogglePermissionRequest extends AbstractValidation
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * @throws \ReflectionException
     */
    public function rules() : array
    {
        return [
            'user_id' => 'required|string',
            'permission' => 'required|in:' . implode(',', AdminPermissionsHelper::permissions()),
        ];
    }
}
