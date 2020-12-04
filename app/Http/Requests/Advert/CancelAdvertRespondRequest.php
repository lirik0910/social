<?php

namespace App\Http\Requests\Advert;

use App\Libraries\GraphQL\AbstractValidation;

class CancelAdvertRespondRequest extends AbstractValidation
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'advert_id' => 'required|integer'
        ];
    }
}
