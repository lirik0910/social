<?php

namespace App\Http\Requests\Chat;

use App\Libraries\GraphQL\AbstractValidation;

class RoomMessagesRequest extends AbstractValidation
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'id' => 'required|string',
            'limit' => 'integer',
            'offset' => 'integer',
        ];
    }

    protected function defaultValues(): array
    {
        return [
            'limit' => 20,
            'offset' => 0
        ];
    }
}
