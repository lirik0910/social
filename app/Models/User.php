<?php

namespace App\Models;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Helpers\NotificationsHelper;
use App\Helpers\PhoneHelper;
use App\Models\Interfaces\CustomEvents;
use App\Traits\ReflectionTrait;
use App\Traits\ThumbsTrait;
use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;
use App\Helpers\MediaHelper;
use Illuminate\Support\Str;

class User extends Authenticatable implements CustomEvents
{
    use HasApiTokens, Notifiable, SoftDeletes, ThumbsTrait, ReflectionTrait;

    const ROLE_USER = 1;
    const ROLE_STAFF = 2;
    const ROLE_MODERATOR = 8;
    const ROLE_ADMIN = 9;
    const ROLE_ROOT = 10;

    const FLAG_REQUIRED_PHONE_VERIFICATION = 0b0000000000000001;
    const FLAG_REQUIRED_FILL_PROFILE = 0b0000000000000010;
    const FLAG_PRIVATE_PROFILE = 0b0000000000000100;
    const FLAG_ENABLED_PHONE_VERIFICATION = 0b0000000000010000;
    const FLAG_PHOTO_VERIFIED_PENDING = 0b0000000000100000;
    const FLAG_PHOTO_VERIFIED = 0b0000000001000000;
    const FLAG_USER_ONLINE = 0b0000000010000000;
    const FLAG_USER_BANNED = 0b0000000100000000;

    const RESET_TOKEN_TIMEOUT = 180; // 3 min
    const EXPIRED_TOKEN_TIMEOUT = 1200; // 20 min

    public $show_private_avatar = false;
    public $blocked_for_user = null;

    public function toArray()
    {
        $array = parent::toArray();
        $array['avatar'] = $this->avatar;
        return $array;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'phone',
        'role',
        'flags',
        'nickname',
        'image',
        'notifications_settings',
        'subscribes_count',
        'subscribers_count',
        'blocked_count',
        'meetings_rating',
        'deleted_at',
        'balance',
        'freezed_balance'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'phone_verification_code',
        'email_verification_code',
        'auth_token',
        'auth_token_expire_at',
    ];

    /**
     * The attributes that should be converted to Carbon instance.
     *
     * @var array
     */
    protected $dates = [
        'deleted_at',
        'phone_verification_expired_at'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'notifications_settings' => 'array'
    ];

    /**
     * Return profile data for user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * Return meeting`s options data for user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function meetings_options()
    {
        return $this->hasOne(UserMeetingsOption::class);
    }

    /**
     * Return private stream`s options data for user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function private_streams_options()
    {
        return $this->hasOne(UserPrivateStreamsOption::class);
    }

    /**
     * Return charity organizations which are added by the user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function charity_organization()
    {
        return $this->hasOne(CharityOrganization::class);
    }

    /**
     * Return auctions which are created by user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function auctions()
    {
        return $this->hasMany(Auction::class);
    }

    /**
     * Return user adverts
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function adverts()
    {
        return $this->hasMany(Advert::class);
    }

    /**
     * Return bids which are made by user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bids()
    {
        return $this->hasMany(AuctionBid::class);
    }

    /**
     * Return public streams which are created by user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function public_streams()
    {
        return $this->hasMany(PublicStream::class);
    }

    /**
     * Return WantWithYou request which was received by user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function received_wants()
    {
        return $this->hasMany(WantWithYou::class, 'user_id');
    }

    /**
     * Return WantWithYou request which made by user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function requested_wants()
    {
        return $this->hasMany(WantWithYou::class, 'who_want_id');
    }

    /** Return public stream views which are made by user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function public_streams_views()
    {
        return $this->hasMany(PublicStreamView::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function blocked_users()
    {
        return $this->hasMany(BlockedUser::class);
    }

    /**
     * Return subscribers
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function subscribers()
    {
        return $this
            ->belongsToMany(User::class, 'subscribes', 'user_id', 'subscriber_id');
    }

    /**
     * Return subscribes
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function subscribes()
    {
        return $this
            ->belongsToMany(User::class, 'subscribes', 'subscriber_id', 'user_id');
    }

    /**
     * Return reports that was sent by user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sent_reports()
    {
        return $this->hasMany(Report::class, 'author_id');
    }

    /**
     * Return reports which was received on user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function received_reports()
    {
        return $this->hasMany(Report::class, 'reported_user_id');
    }

    /**
     * Return media ids which was viewed on feed page
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function feed_viewed()
    {
        return $this->hasOne(FeedViewed::class);
    }

    /**
     * Return backgrounds which uploaded by user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function background()
    {
        return $this->hasOne(ProfilesBackground::class);
    }

    /**
     * Return user devices data
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function devices()
    {
        return $this->hasMany(UserDevice::class);
    }

    /**
     * Find the user instance for the given username.
     *
     * @param string $phone
     *
     * @return \App\Models\User
     */
    public function findForPassport(string $phone)
    {
        return $this->where('phone', $phone)->first();
    }

    /**
     * Send sms to user
     *
     * @param string $message
     *
     * @return bool
     */
    public function sendSms(string $message)
    {
        return PhoneHelper::sendSms($this->phone, $message);
    }

    /**
     * Find user by phone number
     *
     * @param string $phone
     *
     * @return \App\Models\User|NULL
     */
    public static function getUserByPhone(string $phone)
    {
        return self::where('phone', $phone)->first();
    }

    // work with flags

    /**
     * Check whether user has flag
     *
     * @param int $flag
     *
     * @return bool
     */
    public function hasFlag(int $flag)
    {
        return (boolean)($this->flags & $flag);
    }

    /**
     * Add flag to current list of flags
     *
     * @param int $flag
     *
     * @return void
     */
    public function addFlag(int $flag)
    {
        $this->flags |= $flag;
    }

    /**
     * Remove flag from current list of flags
     *
     * @param int $flag
     *
     * @return void
     */
    public function removeFlag(int $flag)
    {
        $this->flags &= ~$flag;
    }

    /**
     * Remove or add flag depending if exists or not
     *
     * @param int $flag
     */
    public function toggleFlag(int $flag)
    {
        $this->flags ^= $flag;
    }

    /**
     * Rewriting current flag(s) with ne one(s)
     *
     * @param int $flag
     */
    public function setFlag(int $flag)
    {
        $this->flags = $flag;
    }

    /**
     * Return users media views
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function views()
    {
        return $this->hasMany(MediaUsersView::class);
    }

    /**
     * Return meetings which user sold
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sold_meetings()
    {
        return $this->hasMany(Meeting::class, 'seller_id');
    }

    /**
     * Return meetings that was purchased by user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function purchased_meetings()
    {
        return $this->hasMany(Meeting::class, 'user_id');
    }

    /**
     * Return private streams that sold by user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sold_private_streams()
    {
        return $this->hasMany(PrivateStream::class, 'seller_id');
    }

    /**
     * Return private streams that purchased by user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function purchased_private_streams()
    {
        return $this->hasMany(PrivateStream::class);
    }

    /**
     * Return presents which was presented on private streams
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function private_stream_presents()
    {
        return $this->hasMany(PrivateStreamPresent::class);
    }

    /**
     * Return the messages which are received by user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function received_private_streams_messages()
    {
        return $this->hasMany(PrivateStreamMessage::class, 'recipient_id');
    }

    /**
     * Return the messages which are sent by user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sent_private_streams_messages()
    {
        return $this->hasMany(PrivateStreamMessage::class);
    }

    /**
     * Return the messages which was sent by user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sent_messages()
    {
        return $this->hasMany(PersonalMessage::class);
    }

    /**
     * Return the messages that was received by user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function received_messages()
    {
        return $this->hasMany(PersonalMessage::class, 'recipient_id');
    }

    /**
     * Return public streams on which user was subscribe
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function public_streams_subscribes()
    {
        return $this->belongsToMany(PublicStream::class, 'public_stream_subscribes', 'user_id', 'public_stream_id');
    }

    /**
     * Return users verifications photos
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function photoVerifications()
    {
        return $this->hasMany(UserPhotoVerification::class);
    }

    /**
     * Return users chat room
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function chat_rooms()
    {
        return $this->hasMany(UsersPrivateChatRoom::class);
    }

    /**
     * Get all messages.
     */
    public function messages()
    {
        return $this->hasMany(UsersPrivateCallMessage::class, 'user_id', 'id');
    }

    /**
     * Return user media
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function media()
    {
        return $this->hasMany(Media::class);
    }

    /**
     * Return reviews for meetings created by user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function meeting_reviews()
    {
        return $this->hasMany(MeetingReview::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptions()
    {
        return $this->hasMany(SubscriberUserPublications::class, 'subscriber_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function publications()
    {
        return $this->hasMany(SubscriberUserPublications::class);
    }

    /**
     * Return responded auctions
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function responded_auctions()
    {
        return $this->belongsToMany(Auction::class, 'auction_bids', 'user_id', 'auction_id');
    }

    /**
     * Return adverts on which user respond
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function responded_adverts()
    {
        return $this->belongsToMany(Advert::class, 'advert_responds', 'user_id', 'advert_id');
    }

    /**
     * Return all ban records for this user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ban_list()
    {
        return $this->hasMany(UserBan::class);
    }

    /**
     * Return current ban record for this user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function current_ban()
    {
        return $this->belongsTo(UserBan::class, 'ban_id');
    }

    /**
     * Get received meeting reviews
     *
     * @return mixed
     */
    public function received_meetings_reviews()
    {
        return MeetingReview
            ::where('user_id', '!=', $this->id)
            ->whereHas('meeting', function ($query) {
                $query
                    ->where('user_id', '=', $this->id)
                    ->orWhere('seller_id', '=', $this->id);
            });
    }

    /**
     * Return all permissions for admin area which user have
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(AdminPermission::class, 'admin_to_permission', 'user_id', 'permission_id');
    }

    /**
     * Calculate time when verification token can be reset
     *
     * @return int
     */
    public function getTimeToResetAttribute()
    {
        if (!$this->phone_verification_expired_at)
            return 0;

        $expired_at = Carbon::now()->diffInSeconds($this->phone_verification_expired_at, false);
        $timeToReset = $expired_at - self::EXPIRED_TOKEN_TIMEOUT + self::RESET_TOKEN_TIMEOUT;

        if ($timeToReset <= 0)
            return 0;

        return $timeToReset;
    }

    /**
     * Get correct avatar url
     *
     * @return string
     * @throws GraphQLLogicRestrictException
     */
    public function getAvatarAttribute()
    {
        if (empty($this->image) || $this->isAvatarHidden()) {
            return null;
        }

        return MediaHelper::getPublicUrl(MediaHelper::getS3Path(MediaHelper::FILE_TYPE_MEDIA_AVATAR) . '/' . $this->id . '/' . $this->image);
    }

    /**
     * Get pivot updated at attribute for user (for subscribes, subscribers, blocked users collections)
     *
     * @return mixed
     */
    public function getUpdatedAtPivotAttribute()
    {
        return $this->pivot->updated_at;
    }

    /**
     * Get pivot deleted at attribute for user (for subscribes, subscribers, blocked users collections)
     *
     * @return mixed
     */
    public function getDeletedAtPivotAttribute()
    {
        return $this->pivot->deleted_at;
    }

    /**
     * Get pivot deleted at attribute for advert (for responded adverts collection)
     *
     * @return mixed
     */
    public function getCreatedAtPivotAttribute()
    {
        return $this->pivot->created_at;
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
     * Determines whether an authorized user is subscribed to this user
     *
     * @param $self
     * @return mixed
     */
    public function isSubscribed($self)
    {
        $user = Auth::user();

        return $self
            ::subscribers()
            ->where('subscriber_id', $user->id)
            ->exists();
    }

    /**
     * Determine if this user and authorized user blocked for each other
     *
     * @throws GraphQLLogicRestrictException
     */
    public function isBlocked()
    {
        $user = Auth::user();

        $blocked_by_me_query = DB
            ::query()
            ->select(DB::raw(1))
            ->from('blocked_users')
            ->where('user_id', $user->id)
            ->where('blocked_id', $this->id);

        $blocked_by_user_query = DB
            ::query()
            ->select(DB::raw(1))
            ->from('blocked_users')
            ->where('user_id', $this->id)
            ->where('blocked_id', $user->id);

        $blocked_info = DB
            ::query()
            ->select([
                'blocked_by_me' => $blocked_by_me_query,
                'blocked_by_user' => $blocked_by_user_query
            ])
            ->get()
            ->toArray()[0];

        if(!empty($blocked_info->blocked_by_me)) {
            throw new GraphQLLogicRestrictException(__('privacy.restriction_from_you'), __('Error!'));
        } elseif(!empty($blocked_info->blocked_by_user) || $this->hasFlag(User::FLAG_PRIVATE_PROFILE)) {
            throw new GraphQLLogicRestrictException(__('privacy.restriction_for_you'), __('Error!'));
        }
    }

    /**
     * Determines if authorized user can view this user avatar
     *
     * @return bool
     */
    protected function isAvatarHidden()
    {
        $auth_user = Auth::user();

        $this->show_private_avatar = $auth_user === $this ? true : false;

        return ($this->hasFlag(self::FLAG_PRIVATE_PROFILE) && !$this->show_private_avatar);
    }

    /**
     * Determine whether the passed notification must be sent
     *
     * @param string $notification
     * @return mixed
     */
    public function isNotifiable(string $notification)
    {
        return Arr::get($this->notifications_settings, $notification);
    }


    /**
     * Return role value by his short name
     *
     * @param $role
     * @return mixed
     */
    public static function getRoleValue($role)
    {
        return constant('self::'. strtoupper('role_' . $role));
    }

    /**
     * @param string|integer $id
     * @throws GraphQLLogicRestrictException
     */
    public function checkProfileAccessibility($id)
    {
        if ($this->role === User::ROLE_USER) {
            $is_blocked_query = BlockedUser
                ::where('user_id', $id)
                ->where('blocked_id', $this->id);

            if($this->id !== $id) {
                if(is_null($this->blocked_for_user)) {
                    if($is_blocked_query->exists()) {
                        throw new GraphQLLogicRestrictException(__('privacy.restriction_for_you'), __('Error!'));
                    } else {
                        $this->blocked_for_user = false;
                    }
                } elseif($this->blocked_for_user) {
                    throw new GraphQLLogicRestrictException(__('privacy.restriction_for_you'), __('Error!'));
                }
            }
        }

        $this->blocked_for_user = false;
    }

    /**
     * Generate slug field
     *
     * @param null $slug
     * @return string|null
     */
    public function generateSlug($slug = null)
    {
        $slug = $slug ?? strtolower($this->nickname);

        if(User::where('slug', $slug)->exists()) {
            if(!preg_match('/\d$/', $slug)) {
                $slug .= 0;
            }

            $slug++;

            return $this->generateSlug($slug);
        }

        return $slug;
    }

    /**
     * @return int
     */
    public function getUnreadedNotificationsCount()
    {
        return $this
            ->unreadNotifications()
            ->count();
    }

    /**
     * Return custom name for eloquent event
     *
     * @param $event_name
     * @return string|null
     */
    public function getCustomEventName($event_name)
    {
        if ($event_name == 'updated' && !empty(Arr::get($this->getChanges(), 'reports_count'))) {
             $event_name = 'reports_count_updated';
        }

        return $event_name;
    }

    /**
     * Return support category which are created by user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function supports()
    {
        return $this->hasMany(Support::class);
    }

    /**
     * Return support messages which are created by user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function supportMessages()
    {
        return $this->hasMany(SupportMessage::class);
    }

    /**
     * Return meetings rating attribute
     *
     * @param $value
     * @return float
     */
    public function getMeetingsRating($value)
    {
        return round($value, 2);
    }
}
