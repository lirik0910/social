<?php

namespace App\GraphQL\Mutations\Payment;

use Carbon\Carbon;
use LiqPay;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class CreateCreditCart
{
    /**
     * @param $rootValue
     * @param array $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     * @return array
     */
    public function resolve($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
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
