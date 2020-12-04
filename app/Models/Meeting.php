<?php

namespace App\Models;

use App\Jobs\MeetingConfirmationCode;
use App\Jobs\MeetingFailedStatusChange;
use App\Jobs\MeetingRateNeededNotificationSend;
use App\Jobs\MeetingRequestDelete;
use App\Models\Interfaces\CustomEvents;
use App\Notifications\Meeting\MeetingStartSoon;
use App\Traits\ReflectionTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class Meeting extends Model implements CustomEvents
{
    use ReflectionTrait, SoftDeletes;

    const PRICE_MAX_VALUE = 40000000;

    const INHERITED_TYPE_AUCTIONS = 'auctions';
    const INHERITED_TYPE_ADVERTS = 'adverts';

    const OUTFIT_CASUAL = 1;
    const OUTFIT_OFFICIAL = 2;
    const OUTFIT_BEACH = 3;
    const OUTFIT_SPORT = 4;

    const STATUS_NEW = 1;
    const STATUS_ACCEPTED = 2;
    const STATUS_CONFIRMED = 3;
    const STATUS_DECLINED = 4;
    const STATUS_FAILED = 5;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'seller_id',
        'location_lat',
        'location_lng',
        'meeting_date',
        'price',
        'outfit',
        'safe_deal',
        'charity_organization_id',
        'address',
        'status'
    ];

    /**
     * The attributes that should be converted to Carbon instance.
     *
     * @var array
     */
    protected $dates = [
        'meeting_date'
    ];

    /**
     * Return the user who created meeting
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Return the user who was invited to he meeting
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Return reports for this meeting
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function reports()
    {
        return $this->morphMany(Report::class, 'reported');
    }

    /**
     * Return charity organizations for meeting credits
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function charity_organization()
    {
        return $this->belongsTo(CharityOrganization::class);
    }

    /**
     * Return auction or advert for meeting
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function inherited()
    {
        return $this->morphTo();
    }

    /**
     * Return meeting reviews
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reviews()
    {
        return $this->hasMany(MeetingReview::class);
    }

    /**
     * @param $event_name
     * @return string|null
     */
    public function getCustomEventName($event_name)
    {
        if($event_name == 'updated' && $this->isDirty()) {
            $new_status = Arr::get($this->getChanges(), 'status');

            if($new_status) {
                switch ($new_status) {
                    case Meeting::STATUS_ACCEPTED:
                        $event_name = 'accepted';
                        break;
                    case Meeting::STATUS_CONFIRMED:
                        $event_name = 'confirmed';
                        break;
                    case Meeting::STATUS_DECLINED:
                        $event_name = 'cancelled';
                        break;
                    case Meeting::STATUS_FAILED:
                        $event_name = 'failed';
                        break;
                    default:
                        break;
                }
            }
        } elseif ($this->deleted_at !== null) {
            $event_name = 'cancelled';
        } else {
            if(!empty($this->inherited_type) && !empty($this->inherited_id)) {
                $event_name = $this->inherited_type === self::INHERITED_TYPE_ADVERTS ? 'created_for_advert' : 'created_for_auction';
            }
        }

        return $event_name;
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
            case MeetingStartSoon::class:
                $result = $this->status === self::STATUS_ACCEPTED;
                break;
            case MeetingConfirmationCode::class:
                $result = $this->status === self::STATUS_ACCEPTED && $this->safe_deal_only;
                break;
            case MeetingRateNeededNotificationSend::class:
                $result = $this->status === self::STATUS_CONFIRMED;
                break;
            case MeetingRequestDelete::class:
                $result = $this->status === self::STATUS_NEW;
                break;
            case MeetingFailedStatusChange::class:
                $result = $this->status !== self::STATUS_CONFIRMED;
                break;
            default:
                break;
        }

        return $result;
    }

    /**
     * Determine if the user create review for this meeting (or return null if user cannot do this)
     *
     * @return bool|null
     */
    public function getReviewExistsAttribute()
    {
        $auth_user = Auth::user();

        if (array_search($auth_user->id, [$this->user_id, $this->seller_id])) {
            $result = $this->reviews()
                ->where('user_id', $auth_user->id)
                ->exists();
        }

        return $result ?? null;
    }

    /**
     * Return decrypted confirmation code
     *
     * @return string
     */
    public function getCodeAttribute()
    {
        $auth_user = Auth::user();

        return $auth_user && $auth_user->id === $this->seller_id ? decrypt($this->confirmation_code) : null;
    }
}
