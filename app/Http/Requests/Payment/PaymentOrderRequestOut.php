<?php

namespace App\Http\Requests\Payment;

use App\Libraries\GraphQL\AbstractValidation;

class PaymentOrderRequestOut extends AbstractValidation
{
    /**
     * {@inheritDoc}
     */
    public function rules() : array
    {
        return [
            'amount' => 'required|integer|min:100',
            'card' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $lunaSum = 0;

                    $value = preg_replace('/\D/', '', $value);

                    foreach (str_split((string)$value, 1) as $key => $number) {
                        if ($key % 2 === 0) {
                            $result = $number * 2;
                            $lunaSum += $result > 9 ? array_sum(str_split((string)$result, 1)) : $result;
                        } else {
                            $lunaSum += $number;
                        }
                    }

                    if ($lunaSum % 10 !== 0)
                        $fail(__('payment.invalid_card_number'));
                }
            ],
        ];
    }
}
