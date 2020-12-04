<?php

namespace App\Http\Requests\Admin\PaymentTransaction;

use App\Libraries\GraphQL\AbstractValidation;
use App\Models\AdminPaymentTransaction;


class ChangeUserBalanceRequest extends AbstractValidation
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
            'id' => 'required|string',
            'amount' => 'required|integer',
            'type' => 'required|integer|in:' . implode(',', array_keys(AdminPaymentTransaction::availableParams('type'))),
        ];
    }
}
