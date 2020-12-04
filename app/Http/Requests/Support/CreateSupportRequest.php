<?php

namespace App\Http\Requests\Support;

use App\Libraries\GraphQL\AbstractValidation;
use App\Models\Support;

class CreateSupportRequest extends AbstractValidation
{
    /**
     * @return array
     * @throws \ReflectionException
     */
    public function rules(): array
    {
        return [
            'category' => 'required|integer|in:' . implode(',', array_keys(Support::availableParams('category'))),
            'message' => 'required|string|min:10|max:500'
        ];
    }
}
