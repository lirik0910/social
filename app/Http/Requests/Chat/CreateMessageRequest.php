<?php

namespace App\Http\Requests\Chat;

use App\Libraries\GraphQL\AbstractValidation;

class CreateMessageRequest extends AbstractValidation
{
    /**
     * @return array
     */
    public function rules() : array
    {
        return [
            'room_id' => 'required|string',
            'message' => 'required|string|min:1|max:255'
        ];
    }
}
