<?php

namespace App\Http\Requests\Admin\Support;

use App\Libraries\GraphQL\AbstractValidation;
use App\Models\Support;


class AllUsersSupportsTotalRequest extends AbstractValidation
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
            'category' => 'required|integer|in:' . implode(',', array_keys(Support::availableParams('category'))),
            'status' => 'integer|in:' . implode(',', array_keys(Support::availableParams('status'))),
            'user' => 'string',
            'moderator' => 'string',
            'only_mine' => 'boolean',
            'updated_date' => 'date',
            'updated_date_period' => 'array',
            'updated_date_period.from' => 'required_with:updated_date_period|date|lte:updated_date_period.to',
            'updated_date_period.to' => 'required_with:updated_date_period|date|gte:updated_date_period.from',
        ];
    }
}
