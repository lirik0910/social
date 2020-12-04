<?php

namespace App\Http\Requests\Chat;

use App\Libraries\GraphQL\AbstractValidation;

class RoomPaymentsTotalRequest extends AbstractValidation
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'ended' => 'boolean',
        ];
    }
}
