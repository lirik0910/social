<?php

namespace App\GraphQl\ResolversOld\Payment;

use LiqPay;
use Carbon\Carbon;

class Payment
{

    /**
     * Credit Cart
     *
     * @param $rootValue
     * @param array $args
     *
     * @return string
     * @throws \App\Exceptions\GraphQLSaveDataException
     */
    public function resolveCreditCart($rootValue, array $args)
    {
        $inputs = $args['data'];
        $liqpay = new LiqPay(env('LIQPAY_PUBLIC_KEY'), env('LIQPAY_PRIVATE_KEY'));
        // TODO Modify for needed logic
        $res = $liqpay->api("request", array(
            'action' => 'auth',
            'version' => '3',
            'phone' => '380682526514',
            'amount' => '0.01',
            'currency' => 'UAH',
            'description' => 'description text',
            'order_id' => Carbon::now()->toDateTimeString(),
            'card' => $inputs['cart'],
            'card_exp_month' => $inputs['month'],
            'card_exp_year' => $inputs['year'],
            'card_cvv' => $inputs['cvv']
        ));

        return [
            'data' => json_encode($res),
        ];
    }

}
