<?php

namespace App\Http\Requests\Admin\Support;

use App\Libraries\GraphQL\AbstractValidation;
use App\Models\Support;

class ChangeSupportCategoryRequest extends AbstractValidation
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
            'id' => 'required|string',
            'category' => 'required|integer|in:' . implode(',', array_keys(Support::availableParams('category'))),
        ];
    }
}
