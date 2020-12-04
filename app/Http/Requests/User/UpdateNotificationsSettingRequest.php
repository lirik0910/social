<?php

namespace App\Http\Requests\User;

use App\Libraries\GraphQL\AbstractValidation;

class UpdateNotificationsSettingRequest extends AbstractValidation
{
    /**
     * @return array
     */
    public function rules() : array
    {
        return [
            'type' => 'required|string',
            'notification' => 'required|string',
            'value' => 'required|boolean'
        ];
    }
}
