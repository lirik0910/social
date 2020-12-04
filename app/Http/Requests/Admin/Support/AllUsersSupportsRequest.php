<?php

namespace App\Http\Requests\Admin\Support;

use App\Libraries\GraphQL\AbstractValidation;
use App\Models\Support;

class AllUsersSupportsRequest extends AbstractValidation
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
            'order_by_dir' => 'string|in:ASC,DESC',
            'filter' => 'array',
            'filter.category' => 'required|integer|in:' . implode(',', array_keys(Support::availableParams('category'))),
            'filter.status' => 'integer|in:' . implode(',', array_keys(Support::availableParams('status'))),
            'filter.user' => 'string',
            'filter.moderator' => 'string',
            'filter.only_mine' => 'boolean',
            'filter.updated_date' => 'date',
            'filter.updated_date_period' => 'array',
            'filter.updated_date_period.from' => 'required_with:filter.updated_date_period|date|lte:filter.updated_date_period.to',
            'filter.updated_date_period.to' => 'required_with:filter.updated_date_period|date|gte:filter.updated_date_period.from',
        ];
    }

    /**
     * @return array|int[]
     */
    protected function defaultValues() : array
    {
        return [
            'limit' => 20,
            'offset' => 0,
            'order_by_dir' => 'DESC',
        ];
    }
}
