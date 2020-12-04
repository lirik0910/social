<?php

namespace App\Http\Requests\Auth;

use App\Libraries\GraphQL\AbstractValidation;

class ForgotPasswordRequest extends AbstractValidation
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'phone' => 'required|string|max:19|regex:/^\+\d+$/i'
        ];
    }
}
