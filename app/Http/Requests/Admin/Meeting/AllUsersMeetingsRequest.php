<?php

namespace App\Http\Requests\Admin\Meeting;

use App\GraphQL\Queries\Admin\PaymentTransaction\AllPaymentTransactions;
use App\Libraries\GraphQL\AbstractValidation;
use App\Models\Meeting;
use App\GraphQL\Queries\Admin\Meeting\AllUsersMeetings;


class AllUsersMeetingsRequest extends AbstractValidation
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
            'order_by.column' => 'string|in:' . implode(',', array_keys(AllUsersMeetings::availableParams('order_by_column'))),
            'filter' => 'array',
            'filter.status' => 'integer|in:' . implode(',', array_keys(Meeting::availableParams('status'))),
            'filter.buyer' => 'string',
            'filter.seller' => 'string',
            'filter.user' => 'string',
            'filter.free' => 'boolean',
            'filter.charity_only' => 'boolean',
            'filter.price_period' => 'array',
            'filter.price_period.from' => 'integer|lte:filter.price_period.to',
            'filter.price_period.to' => 'integer|gte:filter.price_period.from',
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
            'limit' => 10,
            'offset' => 0,
            'order_by.dir' => 'DESC',
            'order_by.column' => AllUsersMeetings::ORDER_BY_COLUMN_UPDATED_DATE,
        ];
    }
}
