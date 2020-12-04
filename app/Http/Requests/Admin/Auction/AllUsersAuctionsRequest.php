<?php

namespace App\Http\Requests\Admin\Auction;

use App\GraphQL\Queries\Admin\Auction\AllUsersAuctions;
use App\Libraries\GraphQL\AbstractValidation;

class AllUsersAuctionsRequest extends AbstractValidation
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
            'order_by.column' => 'string|in:' . implode(',', array_keys(AllUsersAuctions::availableParams('order_by_column'))),
            'filter' => 'array',
            'filter.verified_only' => 'boolean',
            'filter.charity_only' => 'boolean',
            'filter.user' => 'string',
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
        ];
    }
}

