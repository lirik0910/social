<?php

namespace App\Http\Requests\Present;

use App\Libraries\GraphQL\AbstractValidation;

class CreateMediaPresentRequest extends AbstractValidation
{
    /**
     * @return array
     */
    public function rules() : array
    {
        return [
            'media_id' => 'required|integer',
            'present_id' => 'required|integer'
        ];
    }
}
