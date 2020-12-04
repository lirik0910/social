<?php

namespace App\Http\Requests\Admin\Faq;

use App\Helpers\LanguageHelper;
use App\Libraries\GraphQL\AbstractValidation;
use App\Models\FaqCategory;

class FaqCategoriesTotalRequest extends AbstractValidation
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
            'locale' => 'required|string|in:' . implode(',', array_keys(LanguageHelper::availableParams('locale'))),
            'status' => 'boolean',
        ];
    }
}
