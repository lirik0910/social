<?php

namespace App\Http\Requests\Admin\User;

use App\Libraries\GraphQL\AbstractValidation;
use App\Models\User;


class AllAdminUsersTotalRequest extends AbstractValidation
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
            'nickname' => 'string',
            'role' => 'integer|in:' . implode(',', array_keys(User::availableParams('role'))),
            'permissions' => 'array',
            'permissions.*' => 'integer',
            'created_date' => 'date',
            'created_date_period' => 'array',
            'created_date_period.from' => 'required_with:created_date_period|date|lte:created_date_period.to',
            'created_date_period.to' => 'required_with:created_date_period|date|gte:created_date_period.from',
        ];
    }
}
