<?php

namespace App\Http\Requests\Admin\Present;

use App\Helpers\MediaHelper;
use App\Libraries\GraphQL\AbstractValidation;

class CreatePresentRequest extends AbstractValidation
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'category_id' => 'required|string',
            'presents' => 'required|array',
            'presents.*.image' => 'required|file',
            'presents.*.price' => 'required|integer'
        ];
    }
}
