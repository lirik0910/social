<?php

namespace App\Http\Requests\Advert;

use App\Libraries\GraphQL\AbstractValidation;

class AdvertRespondsRequest extends AbstractValidation
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'id' => 'required|integer',
            'limit' => 'nullable|integer',
            'offset' => 'nullable|integer',
        ];
    }
}
