<?php

use Illuminate\Database\Seeder;
use App\Models\PaymentPercent;

class PaymentPercentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PaymentPercent::truncate();

        $percents = [
            [
                'model' => PaymentPercent::PAYMENT_PERCENT_MODEL_MEETING,
                'type' => PaymentPercent::PAYMENT_PERCENT_TYPE_PENALTY,
                'percent' => 10,
                'status' => PaymentPercent::PAYMENT_PERCENT_ENABLE
            ],
            [
                'model' => PaymentPercent::PAYMENT_PERCENT_MODEL_MEETING,
                'type' => PaymentPercent::PAYMENT_PERCENT_TYPE_FEE,
                'percent' => 5,
                'status' => PaymentPercent::PAYMENT_PERCENT_ENABLE
            ],
            [
                'model' => PaymentPercent::PAYMENT_PERCENT_MODEL_MEDIA_PRESENT,
                'type' => PaymentPercent::PAYMENT_PERCENT_TYPE_FEE,
                'percent' => 5,
                'status' => PaymentPercent::PAYMENT_PERCENT_ENABLE
            ],
            [
                'model' => PaymentPercent::PAYMENT_PERCENT_MODEL_PAYMENT_ORDER,
                'type' => PaymentPercent::PAYMENT_PERCENT_TYPE_FEE,
                'percent' => 5,
                'status' => PaymentPercent::PAYMENT_PERCENT_ENABLE
            ],
        ];

        foreach ($percents as $percent) {
            PaymentPercent::create($percent);
        }
    }
}
