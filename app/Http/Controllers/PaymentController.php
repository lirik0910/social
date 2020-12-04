<?php

namespace App\Http\Controllers;

use App\Events\User\BalanceChanged;
use App\Helpers\PaymentTransactionHelper;
use App\Libraries\Payment\FourBill;
use App\Libraries\Payment\FourBillException;
use App\Models\PaymentTransaction;
use Exception;

class PaymentController extends Controller
{
    /**
     * @param $transactionId
     * @throws Exception
     */
    public function index($transactionId){
        $transaction = PaymentTransaction::where([
            ['external_id', '=', $transactionId],
            ['status', '=', PaymentTransaction::TRANSACTION_STATUS_PENDING],
        ])->first();

        if ($transaction) {
            $bill = FourBill::init();

            try {
                $transactionData =  $bill->transactionFind($transactionId);

                if ($transactionData['status'] !== $transaction->external_status) {
                    $transaction->external_status = $transactionData['status'];
                    $transaction->status = FourBill::externalStatusToInternal($transactionData['status']);

                    if ($transaction->status !== PaymentTransaction::TRANSACTION_STATUS_PENDING) {
                        $changed_user = head(PaymentTransactionHelper::changeUserBalance($transaction));

                        if(!empty($changed_user)) {
                            event(new BalanceChanged($changed_user->id));
                        }
                    }

                    \DB::beginTransaction();

                    if (!$transaction->save()) {
                        \DB::rollback();

                        Log::channel('payment')->error("4Bill callback: Failed to update Transaction status. Transaction id $transactionId");
                    }

                    \DB::commit();
                }
            } catch (FourBillException $e) {};
        }
    }
}
