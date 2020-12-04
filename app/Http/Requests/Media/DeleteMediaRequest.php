<?php

namespace App\Http\Requests\Media;

use App\Libraries\GraphQL\AbstractValidation;

class DeleteMediaRequest extends AbstractValidation
{
    /**
     * @return array
     */
    public function rules() : array
    {
        return [
            'id' => 'required|integer'
        ];
    }
}
