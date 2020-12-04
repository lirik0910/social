<?php

namespace App\Http\Requests\User\Block;

use App\Libraries\GraphQL\AbstractValidation;

class BlockUserByPhoneRequest extends AbstractValidation
{
    /**
     * {@inheritDoc}
     */
    public function rules() : array
    {
        return [
            'phone_number' => 'required|string|max:19|regex:/^\+\d+$/i',
            'phone_title' => 'nullable|string'
        ];
    }
}
