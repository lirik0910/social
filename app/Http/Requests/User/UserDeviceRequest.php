<?php

namespace App\Http\Requests\User;

use App\Libraries\GraphQL\AbstractValidation;

class UserDeviceRequest extends AbstractValidation
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'device_id' => 'required|string',
            'device_token' => 'required|string'
        ];
    }
}
