<?php

namespace App\Models;

use App\Traits\ReflectionTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentTransaction extends Model
{
    use SoftDeletes, ReflectionTrait;

    const TRANSACTION_TYPE_OUT = 0;
    const TRANSACTION_TYPE_IN = 1;

    const TRANSACTION_STATUS_PENDING = 0;
    const TRANSACTION_STATUS_FREEZED = 1;
    const TRANSACTION_STATUS_COMPLETED = 2;
    const TRANSACTION_STATUS_CANCELLED = 3;
    const TRANSACTION_STATUS_FAILED = 4;

    const TRANSACTION_SOURCE_TYPE_ADVERT = 'adverts';
    const TRANSACTION_SOURCE_TYPE_ADVERT_RESPOND = 'advertResponds';
    const TRANSACTION_SOURCE_TYPE_AUCTION_BID = 'auctionBids';
    const TRANSACTION_SOURCE_TYPE_MEETING = 'meetings';
    const TRANSACTION_SOURCE_TYPE_MEDIA_PRESENT = 'mediaPresents';
    const TRANSACTION_SOURCE_TYPE_PAYMENT_ORDER = 'paymentOrders';
    const TRANSACTION_SOURCE_TYPE_ADMIN_PAYMENT = 'adminPaymentTransactions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'from_user_id',
        'to_user_id',
        'source_type',
        'source_id',
        'amount',
        'status',
        'type',
        'percent',
        'value',
        'created_at',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function from_user()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function to_user()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function source()
    {
        return $this->morphTo();
    }
}
