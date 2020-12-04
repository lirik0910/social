<?php

namespace App\Http\Requests\Media;

use App\Libraries\GraphQL\AbstractValidation;

class UpdateMediaRequest extends AbstractValidation
{
    /**
     * @return array
     */
    public function rules() : array
    {
        return [
            'id' => 'required|integer',
            'description' => 'nullable|string|max:255',
        ];
    }
}
