<?php

namespace App\Http\Requests\Chat;

use App\Libraries\GraphQL\AbstractValidation;

class GetRoomRequest extends AbstractValidation
{
    /**
     * @return array
     */
    public function rules() : array
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
        ];
    }
}
