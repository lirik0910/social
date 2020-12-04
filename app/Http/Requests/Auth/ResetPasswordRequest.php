<?php

namespace App\Http\Requests\Auth;

use App\Libraries\GraphQL\AbstractValidation;

class ResetPasswordRequest extends AbstractValidation
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'phone' => 'required|string|max:19|regex:/^\+\d+$/i|exists:users,phone',
            'token' => 'required|string',
            'password' => 'required|string|min:8|max:22|regex:/^[a-zA-Z\d@\!\?#\+\-$%^{}\[\]\(\)\~\,\;\:\.\<\>\'\\\"\/\&\*\`]{8,22}$/|confirmed',
        ];
    }
}
