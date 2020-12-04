<?php

namespace App\Http\Requests\Admin\PaymentPercent;

use App\GraphQL\Queries\Admin\PaymentPercent\PaymentPercents;
use App\Libraries\GraphQL\AbstractValidation;
use App\Models\PaymentPercent;


class AllPaymentPercentsRequest extends AbstractValidation
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
            'order_by.dir' => 'in:DESC,ASC',
            'order_by.column' => 'string|in:' . implode(',', array_keys(PaymentPercents::availableParams('order_by_column'))),
            'filter' => 'array',
            'filter.model' => 'integer|in:' . implode(',', array_keys(PaymentPercent::availableParams('payment_percent_model'))),
            'filter.type' => 'integer|in:' . implode(',', array_keys(PaymentPercent::availableParams('payment_percent_type'))),
            'filter.status' => 'boolean',
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
            'order_by.column' => PaymentPercents::ORDER_BY_COLUMN_CREATED_DATE,
            'order_by.dir' => 'DESC'
        ];
    }
}
