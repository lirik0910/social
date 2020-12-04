<?php

namespace App\Http\Requests\Profile;

use App\Libraries\GraphQL\AbstractValidation;

class ChangeBackgroundRequest extends AbstractValidation
{
    /**
     * @return array
     */
    public function rules() : array
    {
        return [
            'id' => 'nullable|integer'
        ];
    }
}
