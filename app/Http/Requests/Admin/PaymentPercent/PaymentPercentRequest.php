<?php

namespace App\Http\Requests\Admin\PaymentPercent;

use App\Libraries\GraphQL\AbstractValidation;
use App\Models\PaymentPercent;

class PaymentPercentRequest extends AbstractValidation
{
    /**
     * {@inheritDoc}
     * @throws \ReflectionException
     */
    public function rules() : array
    {
        return [
            'model' => 'required|integer|in:' . implode(',', array_keys(PaymentPercent::availableParams('payment_percent_model'))),
            'percent' => 'required|integer|min:0|max:100',
            'type' => 'required|integer|in:' . implode(',', array_keys(PaymentPercent::availableParams('payment_percent_type'))),
            'status' => 'required|boolean'
        ];
    }
}
