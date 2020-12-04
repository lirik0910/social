<?php

namespace App\Http\Requests\Admin\ServicePage;

use App\Helpers\LanguageHelper;
use App\Libraries\GraphQL\AbstractValidation;

class CreateServicePageRequest extends AbstractValidation
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
            'title' => 'required|string|min:3|unique:service_pages,title',
            'content' => 'required|string|min:10',
            'slug' => 'string|min:2|max:30',
            'order' => 'integer|min:0',
            'locale' => 'required|string|in:' . implode(',', array_keys(LanguageHelper::availableParams('locale'))),
        ];
    }
}
