<?php

namespace App\Http\Requests\Charity;

use App\Libraries\GraphQL\AbstractValidation;

class CustomCharityRequest extends AbstractValidation
{
    protected $dataLocation = 'data';

    /**
     * {@inheritDoc}
     */
    public function rules() : array
    {
        return [
            'name' => 'required|string',
            'description' => 'nullable|string',
            'link' => 'nullable|url',
        ];
    }
}
