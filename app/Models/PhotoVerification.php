<?php

namespace App\Models;

use App\Helpers\MediaHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PhotoVerification extends Model
{
    const MAX_SIZE = 10485760;

    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'mimetype',
        'size'
    ];

    /**
     * Return user's verification photo
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function verifications()
    {
        return $this->hasMany(UserPhotoVerification::class);
    }

    /**
     * Return public url for user verification photo
     *
     */
    public function getImageUrlAttribute()
    {
        $s3path = MediaHelper::getS3Path(MediaHelper::FILE_TYPE_PHOTO_VERIFICATION_SIGN);

        return MediaHelper::getPublicUrl($s3path . '/' . $this->name);
    }

}
