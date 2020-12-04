<?php

namespace App\Http\Requests\Admin\Present;

use App\Libraries\GraphQL\AbstractValidation;

class CategoryPresentsRequest extends AbstractValidation
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'category_id' => 'required|string|exists:present_categories,id'
        ];
    }
}
