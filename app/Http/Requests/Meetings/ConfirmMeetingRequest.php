<?php

namespace App\Http\Requests\Meetings;

use App\Libraries\GraphQL\AbstractValidation;

class ConfirmMeetingRequest extends AbstractValidation
{
    /**
     * @return array
     */
    public function rules() : array
    {
        return [
            'id' => 'required|integer',
            'confirmation_code' => 'required|string|size:6'
        ];
    }
}
