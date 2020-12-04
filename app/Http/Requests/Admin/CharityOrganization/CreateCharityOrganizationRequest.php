<?php

namespace App\Http\Requests\Admin\CharityOrganization;

use App\Libraries\GraphQL\AbstractValidation;

class CreateCharityOrganizationRequest extends AbstractValidation
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'name' => 'required|string|unique:charity_organizations,name',
            'image' => 'nullable|file',
            'description' => 'required|string',
            'link' => 'nullable|url',
            'payment_receiver_name' => 'string',
            'payment_receiver_address' => 'string',
            'payment_receiver_bank_account' => 'string',
            'payment_receiver_bank' => 'string',
            'payment_receiver_bank_address' => 'string',
        ];
    }
}
