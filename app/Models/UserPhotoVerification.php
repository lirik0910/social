<?php

namespace App\Models;

use App\Helpers\MediaHelper;
use App\Traits\ReflectionTrait;
use Illuminate\Database\Eloquent\Model;

class UserPhotoVerification extends Model
{
    use ReflectionTrait;

    const PHOTO_SIZE = 10240;
    const EXPIRED_PHOTO_VERIFICATION_TIMEOUT = 600; // 10 min

    const STATUS_NEW = 1;
    const STATUS_PENDING = 2;
    const STATUS_ACCEPTED = 3;
    const STATUS_DECLINED = 4;

    const DECLINE_REASON_USER_NOT_MATCHED = 1;
    const DECLINE_REASON_SIGN_NOT_MATCHED = 2;
    const DECLINE_REASON_BAD_AVATAR_QUALITY = 3;
    const DECLINE_REASON_BAD_VERIFICATION_PHOTO_QUALITY = 4;
    const DECLINE_REASON_FAKE_VERIFICATION_PHOTO = 5;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'mimetype',
        'size',
        'verification_expired_at'
    ];

    /**
     * Return user associated with photo verification
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }

    /**
     * Return example photo associated with user photo verification
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function verification_sign()
    {
        return $this->belongsTo(PhotoVerification::class, "verification_photo_id");
    }

    /**
     * Return verifying media
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function media()
    {
        return $this->belongsTo(Media::class);
    }

    /**
     * Return image url attribute
     *
     * @return string
     * @throws \App\Exceptions\GraphQLLogicRestrictException
     */
    public function getImageUrlAttribute()
    {
        $s3path = MediaHelper::getS3Path(MediaHelper::FILE_TYPE_PHOTO_VERIFICATION);

        return MediaHelper::getPublicUrl($s3path . '/' . $this->user_id . '/' . $this->name);
    }
}
