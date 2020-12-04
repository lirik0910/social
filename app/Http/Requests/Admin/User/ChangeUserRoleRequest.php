<?php

namespace App\Http\Requests\Admin\User;

use App\Libraries\GraphQL\AbstractValidation;
use App\Models\User;

class ChangeUserRoleRequest extends AbstractValidation
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
            'role' => 'required|in:' . implode(',', array_flip(User::availableParams('role'))),
        ];
    }
}
