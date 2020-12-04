<?php

namespace App\Models;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Helpers\ConstantHelper;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ReflectionTrait;

class Support extends Model
{
    use ReflectionTrait;

    const STATUS_PENDING = 0;
    const STATUS_IN_PROGRESS = 1;
    const STATUS_CLOSED = 2;

    const CATEGORY_PAYMENT = 1;
    const CATEGORY_MEETING = 2;
    const CATEGORY_AUCTION = 3;
    const CATEGORY_ADVERT = 4;
    const CATEGORY_FEEDBACK = 5;
    const CATEGORY_LOGIN = 6;
    const CATEGORY_ACCOUNT_ACCESS = 7;
    const CATEGORY_UPLOAD = 8;
    const CATEGORY_AVATAR_VERIFICATION = 9;
    const CATEGORY_CHARITY_VERIFICATION = 10;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'category',
        'status',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => self::STATUS_PENDING,
    ];

    /**
     * Get user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the moderator which taken this request
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function moderator()
    {
        return $this->belongsTo(User::class, 'moderator_id', 'id');
    }

    /**
     * Return auctions which are created by user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages()
    {
        return $this->hasMany(SupportMessage::class);
    }

    public function getQuestionAttribute(){
        return $this->messages()->first();
    }

    /**
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null
     */
    public function getCategoryNameAttribute()
    {
        return __('support.' . strtolower(ConstantHelper::getConstantKeyByValue('CATEGORY', self::class, $this->category)));
    }

    /**
     * Return user admin permission name depending of support`s category
     *
     * @param $category
     * @return string
     * @throws GraphQLLogicRestrictException
     */
    public static function getPermissionNameByCategory($category)
    {
        switch ($category) {
            case self::CATEGORY_PAYMENT:
                $action = 'support_payment';
                break;
            case self::CATEGORY_MEETING:
                $action = 'support_meeting';
                break;
            case self::CATEGORY_AUCTION:
                $action = 'support_auction';
                break;
            case self::CATEGORY_ADVERT:
                $action = 'support_advert';
                break;
            case self::CATEGORY_UPLOAD:
                $action = 'support_upload';
                break;
            case self::CATEGORY_FEEDBACK:
                $action = 'support_feedback';
                break;
            case self::CATEGORY_LOGIN:
                $action = 'support_login';
                break;
            case self::CATEGORY_ACCOUNT_ACCESS:
                $action = 'support_account_access';
                break;
            case self::CATEGORY_AVATAR_VERIFICATION:
                $action = 'support_avatar_verification';
                break;
            case self::CATEGORY_CHARITY_VERIFICATION:
                $action = 'support_charity_verification';
                break;
            default:
                throw new GraphQLLogicRestrictException(__('support.incorrect_category_passed'), __('Error!'));
        }

        return $action;
    }
}
