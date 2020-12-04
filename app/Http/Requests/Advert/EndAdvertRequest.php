<?php

namespace App\Http\Requests\Advert;

use App\Libraries\GraphQL\AbstractValidation;

class EndAdvertRequest extends AbstractValidation
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'id' => 'required|integer',
        ];
    }
}
