<?php

namespace App\Http\Requests\Admin\CharityOrganization;

use App\Libraries\GraphQL\AbstractValidation;

class NativeCharityOrganizationsTotalRequest extends AbstractValidation
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'available' => 'boolean',
            'name' => 'string',
        ];
    }
}
