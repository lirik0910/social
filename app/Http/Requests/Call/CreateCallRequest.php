<?php

namespace App\Http\Requests\Call;

use App\Libraries\GraphQL\AbstractValidation;

class CreateCallRequest extends AbstractValidation
{
    /**
     * @return array
     */
    public function rules() : array
    {
        return [
            'callee_user_id' => 'required|integer|exists:users,id',
            'meeting_id' => 'required|integer|exists:meetings,id',
        ];
    }
}
