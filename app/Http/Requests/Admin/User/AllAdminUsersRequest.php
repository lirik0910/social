<?php

namespace App\Http\Requests\Admin\User;

use App\GraphQL\Queries\Admin\User\AllAdminUsers;
use App\Libraries\GraphQL\AbstractValidation;
use App\Models\Profile;
use App\Models\User;


class AllAdminUsersRequest extends AbstractValidation
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
            'order_by' => 'array',
            'order_by.dir' => 'in:ASC,DESC',
            'order_by.column' => 'string|in:' . implode(',', array_keys(AllAdminUsers::availableParams('order_by_column'))),
            'filter' => 'array',
            'filter.nickname' => 'string',
            'filter.role' => 'integer|in:' . implode(',', array_keys(User::availableParams('role'))),
            'filter.permissions' => 'array',
            'filter.permissions.*' => 'integer',
            'filter.created_date' => 'date',
            'filter.created_date_period' => 'array',
            'filter.created_date_period.from' => 'required_with:filter.created_date_period|date|lte:filter.created_date_period.to',
            'filter.created_date_period.to' => 'required_with:filter.created_date_period|date|gte:filter.created_date_period.from',
        ];
    }

    /**
     * @return array|int[]
     */
    protected function defaultValues() : array
    {
        return [
            'limit' => 10,
            'offset' => 0,
            'order_by.column' => 'users.created_at',
            'order_by.dir' => 'DESC',
        ];
    }
}
