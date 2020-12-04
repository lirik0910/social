<?php

namespace App\Http\Requests\Admin\Advert;

use App\GraphQL\Queries\Admin\Advert\AllUsersAdverts;
use App\Libraries\GraphQL\AbstractValidation;
use App\Models\Advert;

class AllUsersAdvertsRequest extends AbstractValidation
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
            'order_by.column' => 'string|in:' . implode(',', array_keys(AllUsersAdverts::availableParams('order_by_column'))),
            'filter' => 'array',
            'filter.type' => 'string|in:' . implode(',', array_keys(Advert::availableParams('type'))),
            'filter.user' => 'string',
            'filter.free' => 'boolean',
            'filter.charity_only' => 'boolean',
            'filter.price_period' => 'array',
            'filter.price_period.from' => 'integer|lte:filter.price_period.to',
            'filter.price_period.to' => 'integer|gte:filter.price_period.from',
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
