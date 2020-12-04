<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::get('/payment/4bill/{transaction_id}/check', 'PaymentController@index');

// TODO remove after testing
//Route::get('/payment/4bill/{transaction_id}/check', function (int $transactionId) {
//    $transaction = \App\Models\PaymentTransaction::where([
//        ['external_id', '=', $transactionId],
//        ['status', '=', PaymentTransaction::TRANSACTION_STATUS_PENDING],
//    ])->first();
//
//    if ($transaction) {
//        $bill = FourBill::init();
//
//        try {
//            $transactionData =  $bill->transactionFind($transactionId);
//
//            if ($transactionData['status'] !== $transaction->external_status) {
//                $transaction->external_status = $transactionData['status'];
//                $transaction->status = FourBill::externalStatusToInternal($transactionData['status']);
//
//                if ($transaction->status !== PaymentTransaction::TRANSACTION_STATUS_PENDING) {
//                    $changed_user = head(PaymentTransactionHelper::changeUserBalance($transaction));
//
//                    if(!empty($changed_user)) {
//                        event(new BalanceChanged($changed_user->id));
//                    }
//                }
//
//                \DB::beginTransaction();
//
//                if (!$transaction->save()) {
//                    \DB::rollback();
//
//                    Log::channel('payment')->error("4Bill callback: Failed to update Transaction status. Transaction id $transactionId");
//                }
//
//                \DB::commit();
//            }
//        } catch (FourBillException $e) {};
//    }
//});