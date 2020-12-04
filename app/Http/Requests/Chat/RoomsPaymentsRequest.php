<?php

namespace App\Http\Requests\Chat;

use App\Libraries\GraphQL\AbstractValidation;

class RoomsPaymentsRequest extends AbstractValidation
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'limit' => 'integer',
            'offset' => 'integer',
            'filter' => 'array',
            'filter.ended' => 'boolean',
        ];
    }
}
