<?php

namespace App\Http\Requests\Support;

use App\Libraries\GraphQL\AbstractValidation;
use App\Models\Support;

class SupportsRequest extends AbstractValidation
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
            'filter.status' => 'integer|in:'  . implode(',', array_keys(Support::availableParams('status'))),
            'filter.category' => 'integer|in:' . implode(',', array_keys(Support::availableParams('category'))),
        ];
    }

    protected function defaultValues(): array
    {
        return [
            'limit' => 10,
            'offset' => 0,
        ];
    }
}
