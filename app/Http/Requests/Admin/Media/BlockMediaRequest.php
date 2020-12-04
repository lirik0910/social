<?php

namespace App\Http\Requests\Admin\Media;

use App\Libraries\GraphQL\AbstractValidation;

class BlockMediaRequest extends AbstractValidation
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'id' => 'required|string',
            'reason' => 'string'
        ];
    }
}
