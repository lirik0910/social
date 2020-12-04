<?php

namespace App\Models;

use App\Models\Interfaces\CustomEvents;
use App\Traits\ReflectionTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Advert extends Model implements CustomEvents
{
    use ReflectionTrait;

    const TYPE_BUY = 1;
    const TYPE_SELL = 2;

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
        'price',
        'min_age',
        'max_age',
        'outfit',
        'photo_verified_only',
        'safe_deal_only',
        'type',
        'preview',
        'charity_organization_id',
        'address',
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
        'cancelled_at',
    ];

    /**
     * Return user who create advert
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Return charity organization for advert
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function charity_organization()
    {
        return $this->belongsTo(CharityOrganization::class);
    }

    /**
     * Return meeting
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function meeting()
    {
        return $this->MorphOne(Meeting::class, 'inherited');
    }

    /**
     * Return users who respond on this advert
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function responds()
    {
        return $this->belongsToMany(User::class, 'advert_responds', 'advert_id', 'user_id');
    }


    /**
     * Return selected respond
     *
     * @return BelongsTo
     */
    public function respond_user()
    {
        return $this->belongsTo(User::class, 'respond_user_id');
    }

    /**
     * Get pivot created at attribute for user (for responded users collection)
     *
     * @return mixed
     */
    public function getCreatedAtPivotAttribute()
    {
        return $this->pivot->created_at;
    }

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
     * Determine whether advert is already ended
     *
     * @return bool
     */
    public function isEnded()
    {
        return ($this->cancelled_at || !empty($this->respond_id) || $this->end_at <= Carbon::now());
    }

    /**
     * Scope a query to only include active adverts
     *
     * @param $query
     * @param $border_date
     * @return mixed
     */
    public function scopeActive($query, $border_date = null)
    {
        $border_date = $border_date ?? DB::raw('NOW()');

        return $query
            ->whereNull('cancelled_at')
            ->where('end_at', '>', $border_date)
            ->whereNull('respond_id');
    }
}
