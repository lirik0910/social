<?php

namespace App\Http\Requests\Admin\PaymentTransaction;

use App\GraphQL\Queries\Admin\PaymentTransaction\AllPaymentTransactions;
use App\Libraries\GraphQL\AbstractValidation;
use App\Models\PaymentTransaction;


class AllPaymentTransactionsRequest extends AbstractValidation
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
            'order_by.column' => 'string|in:' . implode(',', array_keys(AllPaymentTransactions::availableParams('order_by_column'))),
            'filter' => 'array',
            'filter.from_user' => 'string',
            'filter.to_user' => 'string',
            'filter.source_type' => 'string|in:' . implode(',', array_keys(PaymentTransaction::availableParams('transaction_source_type'))),
            'filter.status' => 'integer|in:' . implode(',', array_keys(PaymentTransaction::availableParams('transaction_status'))),
            'filter.amount_period' => 'array',
            'filter.amount_period.from' => 'required_with:filter.amount_period|integer|lte:filter.amount_period.to',
            'filter.amount_period.to' => 'required_with:filter.amount_period|integer|gte:filter.amount_period.from',
            'filter.updated_date' => 'date',
            'filter.updated_date_period' => 'array',
            'filter.updated_date_period.from' => 'required_with:filter.updated_date_period|date|lte:filter.updated_date_period.to',
            'filter.updated_date_period.to' => 'required_with:filter.updated_date_period|date|gte:filter.updated_date_period.from',
        ];
    }
}
