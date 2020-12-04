<?php

namespace App\Http\Requests\Admin\Faq;

use App\Helpers\LanguageHelper;
use App\Libraries\GraphQL\AbstractValidation;

class CreateFaqCategoryRequest extends AbstractValidation
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * @throws \ReflectionException
     */
    public function rules() : array
    {
        return [
            'title' => 'required|string|min:3',
            'locale' => 'required|string|in:' . implode(',', array_keys(LanguageHelper::availableParams('locale'))),
            'order' => 'integer|min:0',
        ];
    }
}
