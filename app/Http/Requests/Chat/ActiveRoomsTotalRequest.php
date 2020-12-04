<?php

namespace App\Http\Requests\Chat;

use App\Libraries\GraphQL\AbstractValidation;
use App\Models\UsersPrivateChatRoom;

class ActiveRoomsTotalRequest extends AbstractValidation
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * @throws \ReflectionException
     */
    public function rules() : array
    {
        return [
            'nickname' => 'string',
            'type' => 'required|integer|in:' . implode(',', array_keys(UsersPrivateChatRoom::availableParams('view_type'))),
        ];
    }
}
