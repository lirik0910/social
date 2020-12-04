<?php


namespace App\Http\Requests\General;

use App\Libraries\GraphQL\AbstractValidation;

class IDRequiredRequest extends AbstractValidation
{

    /**
     * {@inheritDoc}
     */
    public function rules() : array
    {
        return [
            'id' => 'required|string',
        ];
    }
}
