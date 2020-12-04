<?php

namespace App\Http\Requests\User;

use App\Libraries\GraphQL\AbstractValidation;

class UserRequest extends AbstractValidation
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'slug' => 'required|string'
        ];
    }
}
