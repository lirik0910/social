<?php

namespace App\Http\Requests\Admin\Faq;

use App\Helpers\LanguageHelper;
use App\Libraries\GraphQL\AbstractValidation;

class FaqCategoriesRequest extends AbstractValidation
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
            'limit' => 'integer',
            'offset' => 'integer',
            'filter' => 'array',
            'filter.locale' => 'required|string|in:' . implode(',', array_keys(LanguageHelper::availableParams('locale'))),
            'filter.status' => 'boolean',
        ];
    }

    protected function defaultValues() : array
    {
        return [
            'limit' => 10,
            'offset' => 0,
        ];
    }
}
