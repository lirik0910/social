<?php

namespace App\Models;

use App\Jobs\AuctionEndSoonNotification;
use App\Jobs\AuctionMeetingCreate;
use App\Models\Interfaces\CustomEvents;
use App\Traits\ReflectionTrait;
use Carbon\Carbon;
use App\Models\AbstractModels\ProtectedVisibilityAuction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Auction extends Model implements CustomEvents
{
    use ReflectionTrait;

    const STATUS_ONGOING = 1;
    const STATUS_FINISHED = 2;

    const MODELS_NAME = 'Auction';
    const PRICE_MAX_VALUE = 42949967200;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'location_lat',
        'location_lng',
        'meeting_date',
        'input_bid',
        'minimal_step',
        'min_age',
        'max_age',
        'description',
        'outfit',
        'charity_organization_id',
        'photo_verified_only',
        'location_for_winner_only',
        'end_at',
        'address',
        'city',
        'cancelled_at',
    ];

    /**
     * The attributes that should be converted to Carbon instance.
     *
     * @var array
     */
    protected $dates = [
        'meeting_date',
        'end_at',
        'ended_at',
    ];

    /**
     * Return custom event name if it`s happened
     *
     * @param $event_name
     * @return string|null
     */
    public function getCustomEventName($event_name)
    {
        $changed = $this->getChanges();

        if(isset($changed['cancelled_at']) && $event_name == 'updated') {
            $event_name = 'cancelled';
        }

        $this->custom_event = $event_name;

        return $event_name;
    }

    /**
     * Return user who created auction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Return auction bids collection associated with auction and ordered by value
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bids()
    {
        return $this->hasMany(AuctionBid::class);
    }

    /**
     * Return reports for this auction
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function reports()
    {
        return $this->morphMany(Report::class, 'reported');
    }

    /**
     * Return charity organization
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function charity_organization()
    {
        return $this->belongsTo(CharityOrganization::class);
    }

    /**
     * Return auctioned meeting
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function meeting()
    {
        return $this->morphOne(Meeting::class, 'inherited');
    }

    /**
     * Return last bid
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function last_bid()
    {
        return $this->hasOne(AuctionBid::class);
    }

    /**
     * @return Model|\Illuminate\Database\Eloquent\Relations\HasMany|object|null
     */
    public function getLatestBidAttribute()
    {
        return $this->bids()->orderByDesc('value')->first();
    }

    /**
     * Return user newbie status (depending on the registration datetime)
     *
     * @return bool
     */
    public function getNewbieStatusAttribute()
    {
        return Carbon::parse($this->created_at)->diffInDays(now()) <= 7;
    }

    /**
     * Return status of auction
     *
     * @return bool
     */
    public function getStatusAttribute()
    {
        if ($this->cancelled_at || $this->end_at->lessThanOrEqualTo(Carbon::now())) {
            return self::STATUS_FINISHED;
        } else {
            return self::STATUS_ONGOING;
        }
    }

    /**
     * Return auctioned meeting
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function publications()
    {
        return $this->morphMany(SubscriberUserPublications::class, 'pub');
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
     * Determine whether auction is already ended
     *
     * @return bool
     */
    public function isEnded()
    {
        return ($this->cancelled_at || $this->end_at <= Carbon::now());
    }

    /**
     * Determine if the passed job need to be done
     *
     * @param $className
     * @return bool
     */
    public function isJobsNeeded($className)
    {
        $result = false;

        switch ($className) {
            case AuctionEndSoonNotification::class:
                $result = $this->status === self::STATUS_ONGOING;
                break;
            case AuctionMeetingCreate::class:
                $result = is_null($this->cancelled_at) && !empty($this->last_bid_id);
                break;
            default:
                break;
        }

        return $result;
    }

    /**
     * @param $query
     * @param null $border_date
     * @return mixed
     */
    public function scopeActive($query, $border_date = null)
    {
        $border_date = $border_date ?? DB::raw('NOW()');

        return $query
            ->whereNull('cancelled_at')
            ->where('end_at', '>', $border_date);
    }

}
