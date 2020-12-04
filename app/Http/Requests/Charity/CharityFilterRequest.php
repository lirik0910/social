<?php

namespace App\Http\Requests\Charity;

use App\Libraries\GraphQL\AbstractValidation;

class CharityFilterRequest extends AbstractValidation
{
    protected $dataLocation = 'filter';

    /**
     * {@inheritDoc}
     */
    public function rules() : array
    {
        return [
            'search' => 'nullable|string|max:255',
            'limit' => 'nullable|integer',
            'offset' => 'nullable|integer',
        ];
    }

    protected function defaultValues(): array
    {
        return [
            'limit' => 20,
            'offset' => 0,
            'search' => '',
        ];
    }

}
