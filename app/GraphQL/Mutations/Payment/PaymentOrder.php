<?php

namespace App\GraphQL\Mutations\Payment;

use App\Events\User\BalanceChanged;
use App\Exceptions\GraphQLSaveDataException;
use App\Helpers\PaymentTransactionHelper;
use App\Http\Requests\Payment\ConfirmTransactionRequest;
use App\Http\Requests\Payment\PaymentOrderRequestOut;
use App\Http\Requests\Payment\PaymentOrderRequestIn;
use App\Libraries\Payment\FourBill;
use App\Libraries\Payment\FourBillException;
use App\Models\PaymentOrder as PaymentOrderModel;
use App\Models\PaymentTransaction;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException as GraphQLValidationException;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class PaymentOrder
{
    use DynamicValidation;

    private $user;
    private $inputs;
    private $type;

    /**
     * @param $rootValue
     * @param PaymentOrderRequestIn $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     *
     * @return array
     * @throws GraphQLSaveDataException
     */
    protected function in($rootValue, PaymentOrderRequestIn $args, GraphQLContext $context, ResolveInfo $resolveInfo): array
    {
        $this->user = $context->user();
        $this->type = PaymentTransaction::TRANSACTION_TYPE_IN;
        $this->inputs = $args->validated();

        return $this->makeOrder();
    }

    /**
     * @param $rootValue
     * @param PaymentOrderRequestOut $args
     * @param GraphQLContext $context
     * @param ResolveInfo $resolveInfo
     *
     * @return array
     * @throws GraphQLSaveDataException
     * @throws GraphQLValidationException
     */
    protected function out($rootValue, PaymentOrderRequestOut $args, GraphQLContext $context, ResolveInfo $resolveInfo): array
    {
        $this->user = $context->user();
        $this->type = PaymentTransaction::TRANSACTION_TYPE_OUT;
        $this->inputs = $args->validated();

        if ($this->inputs['amount'] > $this->user->balance) {
            throw new GraphQLValidationException(['amount' => __('payment.not_enough_money')], __('Input validation failed.'));
        }

        return $this->makeOrder();
    }

    /**
     * @return array
     * @throws GraphQLSaveDataException
     */
    private function makeOrder(): array
    {
        $order = new PaymentOrderModel();
        $order->user_id = $this->user->id;
        $order->type = $this->type == PaymentTransaction::TRANSACTION_TYPE_IN ? PaymentOrderModel::ORDER_TYPE_IN : PaymentOrderModel::ORDER_TYPE_OUT;

        $order->fill($this->inputs);

        \DB::beginTransaction();

        if (!$order->save()) {
            \DB::rollback();
            throw new GraphQLSaveDataException(__('payment.create_order_filed'), __('Error'));
        }

        $transaction = PaymentTransactionHelper::setPaymentTransactionPercent(
            PaymentTransaction::create([
                'from_user_id' => $this->user->id,
                'to_user_id' => $this->user->id,
                'source_type' => PaymentTransaction::TRANSACTION_SOURCE_TYPE_PAYMENT_ORDER,
                'source_id' => $order->id,
                'type' => $this->type,
                'status' => PaymentTransaction::TRANSACTION_STATUS_PENDING,
                'amount' => $order->amount,
            ])
        );

        $bill = FourBill::init();

        try {
            $transactionData = $bill->transactionCreate(
                $transaction,
                $this->type == PaymentTransaction::TRANSACTION_TYPE_OUT ? $this->inputs['card'] : null
            );
        } catch (FourBillException $e) {
            \DB::rollback();
            throw new GraphQLSaveDataException(__($e->getMessage()), __('Error'));
        }

        $transaction->pay_amount = $transactionData['amount'];
        $transaction->external_id = $transactionData['id'];
        $transaction->currency = $transactionData['currency'];

        if (!$transaction->save()) {
            \DB::rollback();
            throw new GraphQLSaveDataException(__('transaction.update_failed'), __('Error'));
        }

        if ($this->type == PaymentTransaction::TRANSACTION_TYPE_OUT) {
            $changed_user = head(PaymentTransactionHelper::changeUserBalance($transaction));

            if(!empty($changed_user)) {
                event(new BalanceChanged($changed_user->id));
            }
        }

        \DB::commit();

        return $transactionData;
    }
}
