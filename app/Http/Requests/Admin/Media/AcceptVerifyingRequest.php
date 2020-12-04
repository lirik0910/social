<?php

namespace App\Http\Requests\Admin\Media;

use App\Libraries\GraphQL\AbstractValidation;

class AcceptVerifyingRequest extends AbstractValidation
{
    /**
     * @return array
     */
    public function rules() : array
    {
        return [
            'id' => 'required|string'
        ];
    }
}
