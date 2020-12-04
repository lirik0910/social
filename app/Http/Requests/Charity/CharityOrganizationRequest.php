<?php

namespace App\Http\Requests\Charity;

use App\Libraries\GraphQL\AbstractValidation;

class CharityOrganizationRequest extends AbstractValidation
{
    /**
     * {@inheritDoc}
     */
    public function rules() : array
    {
        return [
            'id' => 'required|integer'
        ];
    }
}
