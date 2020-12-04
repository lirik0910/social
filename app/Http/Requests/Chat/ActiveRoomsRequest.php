<?php

namespace App\Http\Requests\Chat;

use App\Libraries\GraphQL\AbstractValidation;
use App\Models\UsersPrivateChatRoom;

class ActiveRoomsRequest extends AbstractValidation
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
            'filter' => 'array',
            'filter.type' => 'required|integer|in:' . implode(',', array_keys(UsersPrivateChatRoom::availableParams('view_type'))),
            'filter.nickname' => 'string',
            'filter.is_closed' => 'boolean',
            'limit' => 'integer',
            'offset' => 'integer',
        ];
    }

    protected function defaultValues(): array
    {
        return [
            'limit' => 20,
            'offset' => 0,
        ];
    }
}
