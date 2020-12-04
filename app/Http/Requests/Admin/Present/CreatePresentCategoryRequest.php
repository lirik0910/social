<?php

namespace App\Http\Requests\Admin\Present;

use App\Helpers\MediaHelper;
use App\Libraries\GraphQL\AbstractValidation;

class CreatePresentCategoryRequest extends AbstractValidation
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'name' => 'required|string|max:16|unique:present_categories',
            'image' => 'required|file',
        ];
    }
}
