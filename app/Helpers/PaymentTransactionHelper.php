<?php

namespace App\Helpers;

use App\Events\User\BalanceChanged;
use App\Exceptions\GraphQLLogicRestrictException;
use App\Exceptions\GraphQLSaveDataException;
use App\Models\AdminPaymentTransaction;
use App\Models\Advert;
use App\Models\AdvertRespond;
use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\CharityOrganization;
use App\Models\Meeting;
use App\Models\PaymentPercent;
use App\Models\PaymentTransaction;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentTransactionHelper
{
    /**
     * @param $event_model
     * @param $event_name
     * @param $object
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     */
    public static function paymentTransaction($event_name, $event_model, $object)
    {
        $functionName = 'get' . $event_model . ucfirst($event_name) . 'Data';

        if(!method_exists(new self, $functionName)) {
            throw new GraphQLLogicRestrictException(__('Function for getting data not found'), __('Error'));
        }

        $transaction_objects = $event_name === 'created'
            ? self::$functionName($event_model, $object)
            : self::$functionName($object);

        if($transaction_objects && count($transaction_objects) > 0) {
            foreach ($transaction_objects as $transaction_object) {
                $paymentTransaction = self::savePaymentTransaction($transaction_object);

                $changed_users = self::changeUserBalance($paymentTransaction);

                if (!empty($changed_users)) {
                    foreach ($changed_users as $changed_user) {
                        event(new BalanceChanged($changed_user));
                    }
                }
            }
        }
    }

    /**
     * @param $modelName
     * @param $object
     * @return array
     * @throws GraphQLLogicRestrictException
     */
    public static function getPaymentOrderCreatedData($modelName, $object)
    {
        // transform object
        $transformedObject = self::transformObject($modelName, $object);

        // set additional params
        $transformedObject['amount'] = $object->amount_with_rate;

        return [
            $transformedObject
        ];
    }

    /**
     * @param $modelName
     * @param $object
     * @return array
     * @throws GraphQLLogicRestrictException
     */
    public static function getMediaPresentCreatedData($modelName, $object)
    {
        // transform object
        $transformedObject = self::transformObject($modelName, $object);

        // set additional params
        $transformedObject['to_user_id'] = $object->media->user_id;
        $transformedObject['type'] = PaymentTransaction::TRANSACTION_TYPE_OUT;

        $paymentTransaction = new PaymentTransaction();
        $paymentTransaction->fill($transformedObject);

        $paymentTransaction = self::setPaymentTransactionPercent($paymentTransaction);

        return [
            $paymentTransaction
        ];
    }

    /**
     * @param $modelName
     * @param $object
     * @return array
     * @throws GraphQLLogicRestrictException
     */
    public static function getAuctionBidCreatedData($modelName, $object)
    {
        // transform object
        $transformedObject = self::transformObject($modelName, $object);

        // set additional params
        $transformedObject['to_user_id'] = $object->auction_user_id;
        $transformedObject['type'] = PaymentTransaction::TRANSACTION_TYPE_OUT;
        $transformedObject['status'] = PaymentTransaction::TRANSACTION_STATUS_FREEZED;

        $transformedObjects[] = $transformedObject;

        // return previous bid
        $previousBidTransformedObject = self::getReturnAuctionBidTransactionData($object);

        if($previousBidTransformedObject) {
            array_push($transformedObjects, $previousBidTransformedObject);
        }

        return $transformedObjects;
    }

    /**
     * @param $object
     * @return array
     */
    public static function getAuctionCancelledData($object)
    {
        if ($object->cancelled_at) {
            $lastBidTransaction = self::getReturnAuctionBidTransactionData($object);

            if ($lastBidTransaction) {
                return [
                    $lastBidTransaction
                ];
            }
        }
    }

    /**
     * @param $modelName
     * @param $object
     * @return array
     * @throws GraphQLLogicRestrictException
     */
    public static function getAdvertCreatedData($modelName, $object)
    {
        if ($object->type == Advert::TYPE_BUY && $object->safe_deal_only) {
            $transformedObject = self::transformObject($modelName, $object);
            $transformedObject['type'] = PaymentTransaction::TRANSACTION_TYPE_OUT;
            $transformedObject['status'] = PaymentTransaction::TRANSACTION_STATUS_FREEZED;
            $transformedObject['to_user_id'] = null;

            return [
                $transformedObject
            ];
        }
    }

    /**
     * @param Advert $object
     * @return array
     * @throws GraphQLSaveDataException
     */
    public static function getAdvertCancelledData(Advert $object)
    {
        if($object->cancelled_at && $object->safe_deal_only && !$object->respond_id) {
            if($object->type === Advert::TYPE_BUY) {
                $lastTransaction = self::getLastTransaction(PaymentTransaction::TRANSACTION_SOURCE_TYPE_ADVERT, $object->id);

                if ($lastTransaction) {
                    $lastTransaction->status = PaymentTransaction::TRANSACTION_STATUS_CANCELLED;

                    return [
                        $lastTransaction
                    ];
                }
            } else {
                self::cancelAdvertRespondsTransactions($object);
            }
        }
    }

    /**
     * @param Advert $object
     * @throws GraphQLSaveDataException
     */
    public static function getAdvertUpdatedData(Advert $object)
    {
        if (!$object->cancelled_at && $object->safe_deal_only && $object->respond_id && $object->type === Advert::TYPE_SELL) {
            self::cancelAdvertRespondsTransactions($object);
        }
    }

    /**
     * Update advert responds transaction`s status on "cancelled" and change users balance
     *
     * @param $object
     * @throws GraphQLSaveDataException
     */
    public static function cancelAdvertRespondsTransactions($object)
    {
        $cancelled_responds = AdvertRespond::where('advert_id', '=', $object->id)->where('user_id', '!=', $object->respond_user_id)->get();

        $lastTransactions = PaymentTransaction::where('source_type', PaymentTransaction::TRANSACTION_SOURCE_TYPE_ADVERT_RESPOND)
            ->whereIn('source_id', $cancelled_responds->pluck('id')->toArray())
            ->where('type', PaymentTransaction::TRANSACTION_TYPE_OUT)
            ->where('status', PaymentTransaction::TRANSACTION_STATUS_FREEZED)
            ->with('from_user')
            ->get();

        if(count($lastTransactions) > 0) {
            $lastTransactions_ids = $lastTransactions->pluck('id')->toArray();
            $from_users_ids = $lastTransactions->pluck('from_user_id')->toArray();

            if (!DB::table('payment_transactions')->whereIn('id', $lastTransactions_ids)->update(['status' => PaymentTransaction::TRANSACTION_STATUS_CANCELLED])) {
                throw new GraphQLSaveDataException(__('payment.save_failed'), __('Error'));
            }

            self::changeUsersBalance($from_users_ids, $object->price, PaymentTransaction::TRANSACTION_STATUS_CANCELLED);

            $from_users = $lastTransactions->pluck('from_user');

            foreach ($from_users as $from_user) {
                event(new BalanceChanged($from_user));
            }
        }
    }

    /**
     * @param $modelName
     * @param $object
     * @return array
     * @throws GraphQLLogicRestrictException
     */
    public static function getAdvertRespondCreatedData($modelName, $object)
    {
        if ($object->advert->type == Advert::TYPE_SELL && $object->advert->safe_deal_only) {
            $transformedObject = self::transformObject($modelName, $object);
            $transformedObject['to_user_id'] = $object->advert->user_id;
            $transformedObject['amount'] = $object->advert->price;
            $transformedObject['type'] = PaymentTransaction::TRANSACTION_TYPE_OUT;
            $transformedObject['status'] = PaymentTransaction::TRANSACTION_STATUS_FREEZED;

            return [
                $transformedObject
            ];
        }
    }

    /**
     * @param $object
     * @return array
     */
    public static function getAdvertRespondDeletedData(AdvertRespond $object)
    {
        if ($object->advert->type == Advert::TYPE_SELL && $object->advert->safe_deal_only) {

            $lastTransaction = self::getLastTransaction(PaymentTransaction::TRANSACTION_SOURCE_TYPE_ADVERT_RESPOND, $object->id);

            if ($lastTransaction) {
                $lastTransaction->status = PaymentTransaction::TRANSACTION_STATUS_CANCELLED;

                return [
                    $lastTransaction
                ];
            }
        }
    }

    /**
     * @param $modelName
     * @param $object
     * @return array
     * @throws GraphQLLogicRestrictException
     */
    public static function getMeetingCreatedData($modelName, $object)
    {
        if (!$object->inherited_type && $object->safe_deal) {
            $transformedObject = self::transformObject($modelName, $object);
            $transformedObject['to_user_id'] = $object->seller_id;
            $transformedObject['amount'] = $object->price;
            $transformedObject['type'] = PaymentTransaction::TRANSACTION_TYPE_OUT;
            $transformedObject['status'] = PaymentTransaction::TRANSACTION_STATUS_FREEZED;

            return [
                $transformedObject
            ];
        }
    }

    /**
     * @param $object
     * @return array
     */
    public static function getMeetingConfirmedData($object)
    {
        $transformedObjects = [];

        // FOR AUCTION
        if ($object->inherited_type == Meeting::INHERITED_TYPE_AUCTIONS && $object->status == Meeting::STATUS_CONFIRMED) {
            $lastBid = AuctionBid
                ::where('auction_id', $object->inherited_id)
                ->orderBy('id', 'desc')
                ->first();

            if($lastBid) {
                $lastTransaction = self::getLastTransaction(PaymentTransaction::TRANSACTION_SOURCE_TYPE_AUCTION_BID, $lastBid->id);

                if ($lastTransaction) {
                    $lastTransaction->status = PaymentTransaction::TRANSACTION_STATUS_COMPLETED;

                    if($lastTransaction) {
                        array_push($transformedObjects, $lastTransaction);
                    }
                }
            }
        }
        // FOR ADVERT
        if ($object->inherited_type == Meeting::INHERITED_TYPE_ADVERTS && $object->status == Meeting::STATUS_CONFIRMED && $object->safe_deal) {
            switch ($object->inherited->type) {
                case Advert::TYPE_BUY:
                    $lastTransaction = self::getLastTransaction(PaymentTransaction::TRANSACTION_SOURCE_TYPE_ADVERT, $object->inherited_id);
                    break;
                case Advert::TYPE_SELL:
                    $lastTransaction = self::getLastTransaction(PaymentTransaction::TRANSACTION_SOURCE_TYPE_ADVERT_RESPOND, $object->inherited->respond_id);
            }

            if(!empty($lastTransaction)) {
                $lastTransaction->status = PaymentTransaction::TRANSACTION_STATUS_COMPLETED;

                if($object->inherited->type === Advert::TYPE_BUY) {
                    $lastTransaction->to_user_id = $object->seller_id;
                }

                array_push($transformedObjects, $lastTransaction);
            }
        }
        // FOR MEETING
        if (!$object->inherited_type && $object->status == Meeting::STATUS_CONFIRMED && $object->safe_deal) {
            $lastTransaction = self::getLastTransaction(PaymentTransaction::TRANSACTION_SOURCE_TYPE_MEETING, $object->id);
            // previous transaction save changes
            if ($lastTransaction) {
                $lastTransaction->status = PaymentTransaction::TRANSACTION_STATUS_COMPLETED;

                array_push($transformedObjects, $lastTransaction);
            }
        }

        return $transformedObjects;
    }

    /**
     * @param $object
     * @return mixed
     */
    public static function getReturnAuctionBidTransactionData($object)
    {
        switch (get_class($object)) {
            case Auction::class:
                $lastBidQuery = AuctionBid::where('auction_id', $object->id)->orderBy('id', 'desc');
                break;
            case AuctionBid::class:
                $lastBidQuery = AuctionBid::where('auction_id', $object->auction_id)->skip(1)->orderBy('id', 'desc');
                break;
            default:
                $lastBidQuery = AuctionBid::where('auction_id', $object->inherited_id)->orderBy('id', 'desc');
                break;
        }

        $lastBid = $lastBidQuery->first();

        if ($lastBid) {
            $lastTransaction = self::getLastTransaction(PaymentTransaction::TRANSACTION_SOURCE_TYPE_AUCTION_BID, $lastBid->id);

            if ($lastTransaction) {
                $lastTransaction->status = PaymentTransaction::TRANSACTION_STATUS_CANCELLED;

                return $lastTransaction;
            }
        }
    }

    /**
     * @param $object
     * @throws GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     */
    public static function getMeetingCancelledData(Meeting $object)
    {
        switch ($object->inherited_type) {
            case Meeting::INHERITED_TYPE_ADVERTS:
                $transactionObjects = self::getReturnAdvertMeetingTransactionData($object);
                break;
            case Meeting::INHERITED_TYPE_AUCTIONS:
                $transactionObjects = self::getReturnAuctionMeetingTransactionData($object);
                break;
            default:
                $transactionObjects = self::getReturnMeetingTransactionData($object);
        }

        return $transactionObjects;
    }

    /**
     * @param $object
     * @throws GraphQLLogicRestrictException
     */
    public static function getMeetingFailedData(Meeting $object)
    {
        switch ($object->inherited_type) {
            case Meeting::INHERITED_TYPE_ADVERTS:
                $transactionObjects = self::getReturnAdvertMeetingTransactionData($object);
                break;
            case Meeting::INHERITED_TYPE_AUCTIONS:
                $transactionObjects = self::getReturnAuctionMeetingTransactionData($object);
                break;
            default:
                $transactionObjects = self::getReturnMeetingTransactionData($object);
        }

        return $transactionObjects;
    }

    /**
     * @param $object
     * @return array
     */
    public static function getReturnAuctionMeetingTransactionData($object)
    {
        if ($object->inherited_type == Meeting::INHERITED_TYPE_AUCTIONS && ($object->status == Meeting::STATUS_DECLINED || $object->status == Meeting::STATUS_FAILED)) {
            $lastTransaction = self::getReturnAuctionBidTransactionData($object);
            $lastTransaction = self::setPaymentTransactionPercent($lastTransaction);

            $penaltyTransaction = self::getMeetingPenaltyTransaction($lastTransaction, $object);

            return [
                $lastTransaction,
                $penaltyTransaction
            ];
        }
    }

    /**
     * @param $object
     * @return array
     */
    public static function getReturnAdvertMeetingTransactionData(Meeting $object)
    {
        if ($object->inherited_type == Meeting::INHERITED_TYPE_ADVERTS && $object->safe_deal && ($object->status == Meeting::STATUS_DECLINED || $object->status == Meeting::STATUS_FAILED)) {
            switch ($object->inherited->type) {
                case Advert::TYPE_BUY:
                    $lastTransaction = self::getLastTransaction(PaymentTransaction::TRANSACTION_SOURCE_TYPE_ADVERT, $object->inherited_id);
                    break;
                case Advert::TYPE_SELL:
                    $lastTransaction = self::getLastTransaction(PaymentTransaction::TRANSACTION_SOURCE_TYPE_ADVERT_RESPOND, $object->inherited->respond_id);
            }

            if(!empty($lastTransaction)) {
                $lastTransaction->status = PaymentTransaction::TRANSACTION_STATUS_CANCELLED;

                if($object->inherited->type === Advert::TYPE_BUY) {
                    $lastTransaction->to_user_id = $object->seller_id;
                }

                $lastTransaction = self::setPaymentTransactionPercent($lastTransaction);

                $penaltyTransaction = self::getMeetingPenaltyTransaction($lastTransaction, $object);

                return [
                    $lastTransaction,
                    $penaltyTransaction
                ];
            }
        }
    }

    /**
     * @param $object
     * @return array
     */
    public static function getReturnMeetingTransactionData(Meeting $object)
    {
        if (!$object->inherited_type && $object->safe_deal && ($object->status == Meeting::STATUS_DECLINED || $object->status == Meeting::STATUS_FAILED)) {
            $lastTransaction = self::getLastTransaction(PaymentTransaction::TRANSACTION_SOURCE_TYPE_MEETING, $object->id);

            if ($lastTransaction) {
                $lastTransaction->status = PaymentTransaction::TRANSACTION_STATUS_CANCELLED;
                $lastTransaction = self::setPaymentTransactionPercent($lastTransaction);

                $penaltyTransaction = self::getMeetingPenaltyTransaction($lastTransaction, $object);

                return [
                    $lastTransaction,
                    $penaltyTransaction
                ];
            }
        }
    }

    /**
     * @param $lastTransaction
     * @param $object
     * @return PaymentTransaction
     */
    public static function getMeetingPenaltyTransaction(PaymentTransaction $lastTransaction, Meeting $object)
    {
        $penaltyTransaction = new PaymentTransaction();
        $penaltyTransaction->from_user_id = $object->status === Meeting::STATUS_DECLINED
            ? Auth::user()->id
            : $lastTransaction->from_user_id;

        $penaltyTransaction->fill([
            'type' => PaymentTransaction::TRANSACTION_TYPE_OUT,
            'source_type' => $lastTransaction->source_type,
            'source_id' => $lastTransaction->source_id,
            'amount' => $lastTransaction->value,
            'status' => PaymentTransaction::TRANSACTION_STATUS_COMPLETED
        ]);

        return $penaltyTransaction;
    }

    /**
     * @param $modelName
     * @param $object
     * @return array
     * @throws GraphQLLogicRestrictException
     */
    public static function getAdminPaymentTransactionCreatedData($modelName, $object)
    {
        $transformedObject = self::transformObject($modelName, $object);

        if ($object->type === AdminPaymentTransaction::TYPE_IN) {
            $transformedObject['type'] = PaymentTransaction::TRANSACTION_TYPE_IN;
        } else {
            $transformedObject['type'] = PaymentTransaction::TRANSACTION_TYPE_OUT;;
        }

        return [
            $transformedObject
        ];
    }

    /**
     * @param $source_type
     * @param $source_id
     * @return mixed
     */
    public static function getLastTransaction(string $source_type, $source_id)
    {
        return PaymentTransaction
            ::where('source_type', $source_type)
            ->where('source_id', $source_id)
            ->where('type', PaymentTransaction::TRANSACTION_TYPE_OUT)
            ->where('status', PaymentTransaction::TRANSACTION_STATUS_FREEZED)
            ->first();
    }

    /**
     * Change user balance
     *
     * @param PaymentTransaction $transaction
     * @return array
     */
    public static function changeUserBalance(PaymentTransaction $transaction)
    {
        $from_user = null;
        $to_user = null;

        switch ($transaction->status) {
            case PaymentTransaction::TRANSACTION_STATUS_FREEZED:
                $from_user = $transaction->from_user_id === Auth::user()->id
                    ? Auth::user()
                    : User::whereId($transaction->from_user_id)->firstOrFail();

                $from_user->update([
                    'balance' => $from_user->balance - $transaction->amount,
                    'freezed_balance' => $from_user->freezed_balance + $transaction->amount
                ]);

                break;
            case PaymentTransaction::TRANSACTION_STATUS_PENDING:
                if($transaction->type === PaymentTransaction::TRANSACTION_TYPE_OUT) {
                    $from_user = Auth::user() && $transaction->from_user_id === Auth::user()->id
                        ? Auth::user()
                        : User::whereId($transaction->from_user_id)->firstOrFail();

                    $from_user->update([
                        'balance' => $from_user->balance - $transaction->amount,
                        'freezed_balance' => $from_user->freezed_balance + $transaction->amount
                    ]);
                }

                break;
            case PaymentTransaction::TRANSACTION_STATUS_COMPLETED:
                if($transaction->type === PaymentTransaction::TRANSACTION_TYPE_IN) {
                    $to_user = User::where('id', $transaction->to_user_id)->firstOrFail();

                    $to_user->increment('balance', $transaction->amount);
                } else {
                    $from_user = $transaction->from_user_id === Auth::user()->id
                        ? Auth::user()
                        : User::whereId($transaction->from_user_id)->firstOrFail();

                    if(empty($transaction->to_user_id) || $transaction->source_type === PaymentTransaction::TRANSACTION_SOURCE_TYPE_ADMIN_PAYMENT) {
                        $from_user->decrement('balance', $transaction->amount);
                    } else {
                        if ($transaction->source_type === PaymentTransaction::TRANSACTION_SOURCE_TYPE_MEDIA_PRESENT) {
                            $from_user->decrement('balance', $transaction->amount);
                        } else {
                            $from_user->decrement('freezed_balance', $transaction->amount);
                        }

                        $to_user = User::where('id', $transaction->to_user_id)->firstOrFail();

                        if(in_array($transaction->source_type, [
                            PaymentTransaction::TRANSACTION_SOURCE_TYPE_MEETING,
                            PaymentTransaction::TRANSACTION_SOURCE_TYPE_ADVERT,
                            PaymentTransaction::TRANSACTION_SOURCE_TYPE_AUCTION_BID,
                            PaymentTransaction::TRANSACTION_SOURCE_TYPE_ADVERT_RESPOND
                        ])) {
                            $charity = self::getTransactionCharityOrganization($transaction->source);

                            if (!empty($charity)) {
                                $charity->increment('balance', $transaction->amount);

                            } else {
                                $to_user->increment('balance', $transaction->amount);
                            }
                        } else {
                            $to_user->increment('balance', $transaction->amount - $transaction->value);
                        }
                    }
                }
                break;
            case PaymentTransaction::TRANSACTION_STATUS_CANCELLED:
            case PaymentTransaction::TRANSACTION_STATUS_FAILED:
                if($transaction->type === PaymentTransaction::TRANSACTION_TYPE_OUT) {
                    $from_user = $transaction->from_user_id === Auth::user()->id
                        ? Auth::user()
                        : User::whereId($transaction->from_user_id)->firstOrFail();

                    $from_user->update([
                        'freezed_balance' => $from_user->freezed_balance - $transaction->amount,
                        'balance' => $from_user->balance + $transaction->amount,
                    ]);
                }
                break;
            default:
                break;
        }

        $changed_users = Arr::where([$from_user, $to_user], function ($value, $key) {
            return !is_null($value);
        });

        return $changed_users ?? [];
    }

    /**
     * Change users balance in case of mass cancellation
     *
     * @param array $users_ids
     * @param int $amount
     * @param int $transactions_status
     */
    public static function changeUsersBalance(array $users_ids, int $amount, int $transactions_status)
    {
        if($transactions_status === PaymentTransaction::TRANSACTION_STATUS_CANCELLED) {
            User::whereIn('id', $users_ids)->decrement('freezed_balance', $amount);
            User::whereIn('id', $users_ids)->increment('balance', $amount);
        }
    }

    /**
     * Return charity organization object or null
     *
     * @param $object
     * @return CharityOrganization|null
     */
    public static function getTransactionCharityOrganization($object)
    {
        switch (get_class($object)) {
            case AuctionBid::class:
                $id = $object->auction->charity_organization_id;
                break;
            case AdvertRespond::class:
                $id = $object->advert->charity_organization_id;
                break;
            case Meeting::class:
            case Advert::class:
                $id = $object->charity_organization_id;
                break;
            default:
                break;
        }

        if(!empty($id)) {
            $charity = CharityOrganization
                ::whereId($id)
                ->whereNull('user_id')
                ->first();
        }


        return $charity ?? null;
    }

    /**
     * transformObject
     *
     * @param $modelName
     * @param $object
     * @return mixed
     * @throws GraphQLLogicRestrictException
     */
    public static function transformObject($modelName, $object)
    {
        // options for replacement
        $keys = [
            'from_user_id' => ['user_id'],
            'to_user_id' => ['seller_id', 'user_id'],
            'amount' => ['amount', 'price', 'sum', 'value'],
            'source_id' => ['id'],
        ];

        // transform object to array
        $transformedObject = $object->toArray();

        // find & replace params from options for replacement
        foreach ($keys as $key => $val) {
            if (!array_key_exists($key, $transformedObject)) {
                $exists_key = key(array_intersect_key($transformedObject, array_flip($val)));
                if ($exists_key) {
                    $transformedObject[$key] = $transformedObject[$exists_key];
                }
            }
        }

        // delete not needed params
        foreach (array_diff_key($transformedObject, $keys) as $not_needed_key => $not_needed_val) {
            unset($transformedObject[$not_needed_key]);
        }

        // set typical params
        $transformedObject['source_type'] = Str::plural(lcfirst($modelName));
        $transformedObject['type'] = $object->type;
        $transformedObject['status'] = PaymentTransaction::TRANSACTION_STATUS_COMPLETED;

        return $transformedObject;
    }

    /**
     * savePaymentTransaction
     *
     * @param $transformedObject
     * @return PaymentTransaction
     * @throws GraphQLSaveDataException
     */
    public static function savePaymentTransaction($transformedObject)
    {
        if(is_array($transformedObject)) {
            $paymentTransaction = new PaymentTransaction();

            $paymentTransaction->fill($transformedObject);
        } else {
            $paymentTransaction = $transformedObject;
        }

        if (!$paymentTransaction->save()) {
            throw new GraphQLSaveDataException(__('payment.save_failed'), __('Error'));
        }

        return $paymentTransaction;
    }

    /**
     * @param $paymentTransaction
     * @return mixed
     */
    public static function setPaymentTransactionPercent($paymentTransaction)
    {
        switch ($paymentTransaction->source_type) {
            case PaymentTransaction::TRANSACTION_SOURCE_TYPE_MEETING:
            case PaymentTransaction::TRANSACTION_SOURCE_TYPE_ADVERT_RESPOND:
            case PaymentTransaction::TRANSACTION_SOURCE_TYPE_ADVERT:
            case PaymentTransaction::TRANSACTION_SOURCE_TYPE_AUCTION_BID:
                $percent_model = PaymentPercent::PAYMENT_PERCENT_MODEL_MEETING;
                break;
            case PaymentTransaction::TRANSACTION_SOURCE_TYPE_MEDIA_PRESENT:
                $percent_model = PaymentPercent::PAYMENT_PERCENT_MODEL_MEDIA_PRESENT;
                break;
            case PaymentTransaction::TRANSACTION_SOURCE_TYPE_PAYMENT_ORDER:
                $percent_model = PaymentPercent::PAYMENT_PERCENT_MODEL_PAYMENT_ORDER;
                break;
            default:
                break;
        }

        // If source was cancelled by admin - percent is not needed
        if (Auth::user() && Auth::user()->role !== User::ROLE_USER) {
            $percent_model = null;
        }

        if (!empty($percent_model)) {
            $percent_type = $paymentTransaction->status === PaymentTransaction::TRANSACTION_STATUS_CANCELLED
                ? PaymentPercent::PAYMENT_PERCENT_TYPE_PENALTY
                : PaymentPercent::PAYMENT_PERCENT_TYPE_FEE;

            $percent = PaymentPercent::where('status', true)
                ->where('model', $percent_model)
                ->where('type', $percent_type)
                ->first();

            if (!empty($percent) && $percent->percent > 0) {
                $percentValue = round($paymentTransaction->amount * $percent->percent / 100);

                $paymentTransaction->percent = $percent->percent;
                $paymentTransaction->value = $percentValue;
            }
        }

        return $paymentTransaction;
    }
}
