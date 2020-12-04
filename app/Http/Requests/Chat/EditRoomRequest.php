<?php

namespace App\Http\Requests\Chat;

use App\Libraries\GraphQL\AbstractValidation;
use Carbon\Carbon;

class EditRoomRequest extends AbstractValidation
{
    /**
     * @return array
     */
    public function rules() : array
    {
        return [
            'room_id' => 'required|string',
            'price' => 'required|integer|min:0',
        ];
    }
}
