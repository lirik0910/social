<?php

namespace App\Http\Requests\Payment;

use App\Libraries\GraphQL\AbstractValidation;

class PaymentOrderRequestIn extends AbstractValidation
{
    /**
     * {@inheritDoc}
     */
    public function rules() : array
    {
        return [
            'amount' => 'required|integer|min:100',
        ];
    }
}
