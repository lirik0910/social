<?php

namespace App\Models;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Helpers\MediaHelper;
use App\Models\Interfaces\CustomEvents;
use App\Traits\ReflectionTrait;
use App\Traits\ThumbsTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class Media extends Model implements CustomEvents
{
    use SoftDeletes, ReflectionTrait, ThumbsTrait;

    const MAX_SIZE = 209715200;

    const TYPE_IMAGE = 1;
    const TYPE_VIDEO = 2;
    const TYPE_AVATAR = 3;

    const STATUS_NOT_VERIFIED = 0;
    const STATUS_VERIFYING_PENDING = 1;
    const STATUS_VERIFIED = 2;
    const STATUS_BANNED = 3;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'name',
        'mimetype',
        'size',
        'description',
        'reason',
        'status',
    ];


    /**
     * Return user associated with media
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }

    /**
     * Return users view for media
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users_views()
    {
        return $this->hasMany(MediaUsersView::class);
    }

    /**
     * Return media presents
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function presents()
    {
        return $this->hasMany(MediaPresent::class);
    }

    /**
     * Return reports for this media
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function reports()
    {
        return $this->morphMany(Report::class, 'reported');
    }

    /**
     * Return favorite model for media
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function favorite()
    {
        return $this->morphOne(SubscriberUserPublications::class, 'pub');
    }

    /**
     * Return public uri for media
     *
     * @return string
     * @throws GraphQLLogicRestrictException
     */
    public function getMediaUriAttribute()
    {
        switch($this->type){
            case self::TYPE_IMAGE:
                $file_type = MediaHelper::FILE_TYPE_MEDIA_IMAGE;
                break;
            case self::TYPE_VIDEO:
                $file_type = MediaHelper::FILE_TYPE_MEDIA_VIDEO;
                break;
            case self::TYPE_AVATAR:
                $file_type = MediaHelper::FILE_TYPE_MEDIA_AVATAR;
                break;
            default:
                return null;
                break;
        }

        $s3path = MediaHelper::getS3Path($file_type);

        return MediaHelper::getPublicUrl($s3path . '/' . $this->user_id . '/' . $this->name);
    }

    /**
     * Get presents for media determine the user
     *
     * @return \Illuminate\Database\Eloquent\Collection|null
     */
    public function getPresents()
    {
        $presents = null;

        if($this->user_id === Auth::user()->id) {
            $presents = $this
                ->presents()
                ->orderByDesc('created_at')
                ->get();
        }

        return $presents;
    }

    public function getCustomEventName($event_name)
    {
        if($event_name == 'updated' && $this->isDirty()) {
            $new_status = Arr::get($this->getChanges(), 'status');

            if(!is_null($new_status)) {
                switch ($new_status) {
                    case self::STATUS_VERIFIED:
                    case self::STATUS_VERIFYING_PENDING:
                    case self::STATUS_NOT_VERIFIED:
                        $event_name = 'verification';
                        break;
                    case self::STATUS_BANNED:
                        $event_name = 'banned';
                        break;
                    default:
                        break;
                }
            }
        }

        return $event_name;
    }

    public function scopeNotBanned($query)
    {
        return $query
            ->where('status', '!=', Media::STATUS_BANNED)
            ->orWhereNull('status');
    }
}
