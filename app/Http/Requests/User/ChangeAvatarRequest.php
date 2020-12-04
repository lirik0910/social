<?php

namespace App\Http\Requests\User;

use App\Libraries\GraphQL\AbstractValidation;

class ChangeAvatarRequest extends AbstractValidation
{
    /**
     * @return array
     */
    public function rules() : array
    {
        return [
            'id' => "required|integer"
        ];
    }
}
