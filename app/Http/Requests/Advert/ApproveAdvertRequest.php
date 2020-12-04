<?php

namespace App\Http\Requests\Advert;

use App\Libraries\GraphQL\AbstractValidation;

class ApproveAdvertRequest extends AbstractValidation
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'advert_id' => 'required|integer',
            'user_id' => 'required|integer'
        ];
    }
}
