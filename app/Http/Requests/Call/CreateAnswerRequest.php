<?php

namespace App\Http\Requests\Call;

use App\Libraries\GraphQL\AbstractValidation;

class CreateAnswerRequest extends AbstractValidation
{
    /**
     * @return array
     */
    public function rules() : array
    {
        return [
            'parent_id' => 'required|integer|exists:users_private_calls,id',
            'caller_user_id' => 'required|integer|exists:users,id',
            'callee_user_id' => 'required|integer|exists:users,id',
            'action' => 'required|integer',
            'meeting_id' => 'required|integer|exists:meetings,id',
        ];
    }
}
