<?php

namespace App\Http\Requests\Notification;

use App\Libraries\GraphQL\AbstractValidation;

class ReadNotificationRequest extends AbstractValidation
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'id' => 'string'
        ];
    }
}
