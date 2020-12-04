<?php

namespace App\Http\Requests\Meetings;

use App\Libraries\GraphQL\AbstractValidation;

class AcceptMeetingRequest extends AbstractValidation
{
    /**
     * @return array
     */
    public function rules() : array
    {
        return [
            'id' => 'required|integer'
        ];
    }
}
