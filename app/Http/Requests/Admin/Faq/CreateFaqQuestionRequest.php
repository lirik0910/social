<?php

namespace App\Http\Requests\Admin\Faq;

use App\Libraries\GraphQL\AbstractValidation;

class CreateFaqQuestionRequest extends AbstractValidation
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
            'title' => 'required|string|min:3',
            'content' => 'required|string|min:10',
            'order' => 'integer|min:0',
        ];
    }
}
