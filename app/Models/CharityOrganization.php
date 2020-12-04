<?php

namespace App\Models;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Helpers\MediaHelper;
use App\Traits\ReflectionTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CharityOrganization extends Model
{
    use ReflectionTrait;

    const MODERATION_STATUS_PENDING = 0;
    const MODERATION_STATUS_DECLINED = 1;
    const MODERATION_STATUS_APPROVED = 2;

    const MODERATION_DECLINED_REASON_ADVERTISING = 1;
    const MODERATION_DECLINED_REASON_INSULTS = 2;
    const MODERATION_DECLINED_REASON_INAPPROPRIATE_CONTENT = 3;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'image',
        'name',
        'description',
        'link',
        'payment_receiver_name',
        'payment_receiver_address',
        'payment_receiver_bank',
        'payment_receiver_bank_address',
        'payment_receiver_bank_account',
        'available',
    ];

    /**
     * Return user who added the organization
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Return users meetings options data for charity organization
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function user_meetings_options()
    {
        return $this->hasMany(UserMeetingsOption::class);
    }

    /**
     * Return auction
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function auctions()
    {
        return $this->HasMany(Auction::class);
    }

    /**
     * Return adverts
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function adverts()
    {
        return $this->hasMany(Advert::class);
    }

    /**
     * Return meeting
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function meetings()
    {
        return $this->hasMany(Meeting::class);
    }

    /**
     * Get the charity organization correct image url
     *
     * @param $value
     * @return string
     * @throws GraphQLLogicRestrictException
     */
    public function getImageUrlAttribute($value)
    {
        $s3path = MediaHelper::getS3Path(MediaHelper::FILE_TYPE_CHARITY_ORGANIZATION_IMAGE);

        return MediaHelper::getPublicUrl($s3path . '/' . $this->image);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeAvailable($query)
    {
        $user = Auth::user();

        return $query
            ->where(function ($query) {
                $query
                    ->whereNull('user_id')
                    ->where('available', true);
            })
            ->orWhere('user_id', $user->id);
    }
}
