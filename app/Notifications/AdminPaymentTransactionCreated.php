<?php

namespace App\Notifications;

use App\Libraries\GraphQL\AbstractNotification;
use App\Models\AdminPaymentTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AdminPaymentTransactionCreated extends AbstractNotification
{
    use Queueable;

    const EVENT_TYPE = 'admin_payment_transaction.created';

    /**
     * @var AdminPaymentTransaction
     */
    protected $admin_payment_transaction;

    /**
     * Create a new notification instance.
     *
     * @param AdminPaymentTransaction $admin_payment_transaction
     * @return void
     */
    public function __construct(AdminPaymentTransaction $admin_payment_transaction)
    {
        $this->admin_payment_transaction = $admin_payment_transaction;
        $this->data = $this->getData();
    }

    protected function getData($notifiable = null)
    {
        return [
            'info' => [
                'admin_payment_transaction_id' => $this->admin_payment_transaction->id,
                'admin_payment_transaction_type' => $this->admin_payment_transaction->type,
                'admin_payment_transaction_amount' => $this->admin_payment_transaction->amount,
            ],
            'type' => self::EVENT_TYPE
        ];
    }
}
