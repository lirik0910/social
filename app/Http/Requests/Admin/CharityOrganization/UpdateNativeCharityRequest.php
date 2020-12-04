<?php

namespace App\Http\Requests\Admin\CharityOrganization;

use App\Libraries\GraphQL\AbstractValidation;

class UpdateNativeCharityRequest extends AbstractValidation
{
    /**
     * @return array
     */
    public function rules() : array
    {
        return [
            'id' => 'required|integer',
            'name' => 'required|string',
            'image' => 'nullable|file',
            'description' => 'required|string',
            'link' => 'nullable|url',
            'payment_receiver_name' => 'string',
            'payment_receiver_address' => 'string',
            'payment_receiver_bank_account' => 'string',
            'payment_receiver_bank' => 'string',
            'payment_receiver_bank_address' => 'string',
            'available' => 'boolean',
        ];
    }
}
