<?php

namespace App\Models;

use App\Models\Interfaces\CustomEvents;
use App\Traits\ReflectionTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;

class PaymentOrder extends Model implements CustomEvents
{
    use SoftDeletes, ReflectionTrait;

    const ORDER_TYPE_IN = 1;
    const ORDER_TYPE_OUT = 0;

    const ORDER_STATUS_PENDING = 0;
    const ORDER_STATUS_COMPLETED = 1;
    const ORDER_STATUS_FAILED = 2;

    const EXCHANGE_RATE = 100;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'amount',
        'type',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return float|int
     */
    public function getAmountWithRateAttribute()
    {
        return $this->amount * self::EXCHANGE_RATE;
    }

    /**
     * Return auctioned transactions
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function payment_transactions()
    {
        return $this->morphMany(PaymentTransaction::class, 'source');
    }

    /**
     * @param $event_name
     * @return string|null
     */
    public function getCustomEventName($event_name)
    {
        if ($event_name == 'updated') {
            if ($this->type === PaymentOrder::ORDER_TYPE_IN) {
                $event_name = 'in';
            } else {
                $event_name = 'out';
            }
        }

        return $event_name;
    }
}
