<?php

namespace App\Http\Requests\Admin\Present;

use App\Libraries\GraphQL\AbstractValidation;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePresentCategoryRequest extends AbstractValidation
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
            'name' => 'string|unique:present_categories',
            'image' => 'file',
            'available' => 'boolean'
        ];
    }
}
