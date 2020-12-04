<?php

namespace App\Http\Requests\Auth;

use App\Libraries\GraphQL\AbstractValidation;

class ConfirmSmsRequest extends AbstractValidation
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'code' => 'required|digits:6'
        ];
    }
}
