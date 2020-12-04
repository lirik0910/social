<?php

namespace App\Http\Requests\Admin\Present;

use App\Libraries\GraphQL\AbstractValidation;

class UpdatePresentRequest extends AbstractValidation
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
            'image' => 'nullable|file',
            'price' => 'integer|min:0'
        ];
    }
}
