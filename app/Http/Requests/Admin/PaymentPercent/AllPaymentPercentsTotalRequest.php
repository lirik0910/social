<?php

namespace App\Http\Requests\Admin\PaymentPercent;

use App\Libraries\GraphQL\AbstractValidation;
use App\Models\PaymentPercent;


class AllPaymentPercentsTotalRequest extends AbstractValidation
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
            'model' => 'integer|in:' . implode(',', array_keys(PaymentPercent::availableParams('payment_percent_model'))),
            'type' => 'integer|in:' . implode(',', array_keys(PaymentPercent::availableParams('payment_percent_type'))),
            'status' => 'boolean',
        ];
    }
}
