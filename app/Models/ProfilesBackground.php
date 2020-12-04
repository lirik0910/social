<?php

namespace App\Models;

use App\Exceptions\GraphQLLogicRestrictException;
use App\Traits\ThumbsTrait;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\MediaHelper;

class ProfilesBackground extends Model
{
    use ThumbsTrait;

    const BUCKET_ROOT_PATH = 'profiles_backgrounds';

    const MAX_SIZE = 904857600;

    const TYPE_IMAGE = 1;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'size',
        'mimetype'
    ];

    /**
     * Return user who upload a background
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Return public uri for profiles background
     *
     * @return string
     * @throws GraphQLLogicRestrictException
     */
    public function getImageUrlAttribute()
    {
        $s3path = MediaHelper::getS3Path(MediaHelper::FILE_TYPE_PROFILE_BACKGROUND);

        if($this->user_id) {
            $uri = MediaHelper::getPublicUrl($s3path . '/users/' . $this->name);
        } else {
            $uri = MediaHelper::getPublicUrl($s3path . '/' . $this->name);
        }

        return $uri;
    }
}
