<?php

namespace App\Notifications\PaymentOrder;

use App\Libraries\GraphQL\AbstractNotification;
use App\Models\PaymentOrder;
use Illuminate\Bus\Queueable;

class PaymentOrderOut extends AbstractNotification
{
    use Queueable;

    const EVENT_TYPE = 'payment_order.out';

    /**
     * Payment order
     *
     * @var PaymentOrder
     */
    protected $payment_order;

    /**
     * Create a new notification instance.
     *
     * @param PaymentOrder $payment_order
     * @return void
     */
    public function __construct(PaymentOrder $payment_order)
    {
        $this->payment_order = $payment_order;
        $this->data = $this->getData();
    }

    /**
     * @param null $notifiable
     * @return array|void
     */
    protected function getData($notifiable = null)
    {
        return [
            'info' => [
                'payment_order_id' => (string) $this->payment_order->id,
                'payment_order_type' => $this->payment_order->type,
                'payment_order_amount' => $this->payment_order->amount,
            ],
            'type' => self::EVENT_TYPE,
        ];
    }
}
