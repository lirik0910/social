<?php

namespace App\Http\Requests\Media;

use App\Libraries\GraphQL\AbstractValidation;

class ViewMediaRequest extends AbstractValidation
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
