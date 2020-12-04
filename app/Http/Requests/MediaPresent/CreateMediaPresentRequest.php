<?php

namespace App\Http\Requests\MediaPresent;

use App\Libraries\GraphQL\AbstractValidation;

class CreateMediaPresentRequest extends AbstractValidation
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'media_id' => 'required|string',
            'present_id' => 'required|string'
        ];
    }
}
