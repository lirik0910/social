<?php

namespace App\Http\Requests\Chat;

use App\Libraries\GraphQL\AbstractValidation;

class CreateRoomRequest extends AbstractValidation
{
    /**
     * @return array
     */
    public function rules() : array
    {
        return [
            'user_id' => 'required|string',
            'message' => 'required|string|min:1|max:255',
        ];
    }
}
