<?php

namespace App\Http\Requests\Call;

use App\Libraries\GraphQL\AbstractValidation;

class ChangeStatusRequest extends AbstractValidation
{
    /**
     * @return array
     */
    public function rules() : array
    {
        return [
            'id' => 'required|integer',
        ];
    }
}
