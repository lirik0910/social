<?php

namespace App\Http\Requests\Admin\User;

use App\Libraries\GraphQL\AbstractValidation;

class ChangeAdminPermissionRequest extends AbstractValidation
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'permission_id' => 'required|string',
            'user_id' => 'required|string',
        ];
    }
}
