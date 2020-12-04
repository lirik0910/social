<?php

namespace App\Models;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Traits\ReflectionTrait;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use ReflectionTrait;

    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;
    const STATUS_DECLINED = 2;

    const TYPE_USERS = 'users';
    const TYPE_MEDIA = 'media';
    const TYPE_MEETINGS = 'meetings';
    const TYPE_AUCTIONS = 'auctions';
    const TYPE_ADVERTS = 'adverts';
    const TYPE_CHAT_ROOMS = 'privateChatRooms';

    const REASON_USERS_FRAUDER = 1;
    const REASON_USERS_ILLEGAL_CONTENT = 2;

    const REASON_MEDIA_ADULT_CONTENT = 1;
    const REASON_MEDIA_VIOLENCE = 2;
    const REASON_MEDIA_DANGEROUS_ACTS = 3;
    const REASON_MEDIA_INSULT = 4;
    const REASON_MEDIA_SPAM = 5;

    const REASON_MEETINGS_PARTNER_FRAUDER = 1;
    const REASON_MEETINGS_PARTNER_DIDNT_COME = 2;
    const REASON_MEETINGS_PARTNER_INAPPROPRIATE = 3;

    const REASON_ADVERTS_FRAUD = 1;
    const REASON_ADVERTS_SPAM = 2;

    const REASON_AUCTIONS_FRAUD = 1;
    const REASON_AUCTIONS_SPAM = 2;
    const REASON_AUCTIONS_INSULT = 3;

    const REASON_CHAT_ROOMS_FRAUD = 1;
    const REASON_CHAT_ROOMS_SPAM = 2;
    const REASON_CHAT_ROOMS_INSULT = 3;
    const REASON_CHAT_ROOMS_ADULT_CONTENT = 4;

    const LIMIT_COUNT_FOR_NOTIFICATION_SENDING = 10;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'reported_type',
        'reported_id',
        'reason',
    ];

    /**
     * Get report`s author
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }

    /**
     * Return reported entity
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function reported()
    {
        return $this->morphTo();
    }

    /**
     * Return reported user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reported_user()
    {
        return $this->belongsTo(User::class, 'reported_user_id');
    }

    /**
     * Get report types and reasons for settings
     *
     * @return array
     * @throws \ReflectionException
     */
    public static function getSettings()
    {
        $types = self::availableParams('type');

        foreach($types as $type) {
            $settings[$type] = self::availableParams('reason_' . $type, __('report.reason_' . $type));
        }

        return $settings ?? [];
    }

    /**
     * Return user admin permission name depending of report`s type
     *
     * @param $type
     * @return string
     * @throws GraphQLLogicRestrictException
     */
    public static function getPermissionNameByType($type)
    {
        switch ($type) {
            case self::TYPE_ADVERTS:
                $action = 'report_advert';
                break;
            case self::TYPE_USERS:
                $action = 'report_user';
                break;
            case self::TYPE_MEDIA:
                $action = 'report_media';
                break;
            case Report::TYPE_AUCTIONS:
                $action = 'report_auction';
                break;
            case Report::TYPE_MEETINGS:
                $action = 'report_meeting';
                break;
            case Report::TYPE_CHAT_ROOMS:
                $action = 'report_chat_room';
                break;
            default:
                throw new GraphQLLogicRestrictException(__('report.incorrect_type_passed'), __('Error!'));
        }

        return $action;
    }
}
