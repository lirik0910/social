<?php

namespace App\Http\Requests\Admin\Faq;

use App\Libraries\GraphQL\AbstractValidation;

class EditFaqQuestionRequest extends AbstractValidation
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
            'title' => 'string|min:3',
            'content' => 'string|min:10',
            'status' => 'boolean',
            'order' => 'integer|min:0',
        ];
    }
}
