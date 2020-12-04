<?php

namespace App\Http\Requests\Admin\PaymentTransaction;

use App\Libraries\GraphQL\AbstractValidation;
use App\Models\PaymentTransaction;

class AllPaymentTransactionsTotalRequest extends AbstractValidation
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
            'from_user' => 'string',
            'to_user' => 'string',
            'source_type' => 'string|in:' . implode(',', array_keys(PaymentTransaction::availableParams('transaction_source_type'))),
            'amount_period' => 'array',
            'amount_period.from' => 'required_with:amount_period|integer|lte:amount_period.to',
            'amount_period.to' => 'required_with:amount_period|integer|gte:amount_period.from',
            'updated_date' => 'date',
            'updated_date_period' => 'array',
            'updated_date_period.from' => 'required_with:updated_date_period|date|lte:updated_date_period.to',
            'updated_date_period.to' => 'required_with:updated_date_period|date|gte:updated_date_period.from',
        ];
    }
}
