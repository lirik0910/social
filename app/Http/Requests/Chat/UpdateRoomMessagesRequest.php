<?php

namespace App\Http\Requests\Chat;

use App\Libraries\GraphQL\AbstractValidation;

class UpdateRoomMessagesRequest extends AbstractValidation
{
    /**
     * @return array
     */
    public function rules() : array
    {
        return [
            'id' => 'required|string'
        ];
    }
}
