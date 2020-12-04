<?php

namespace App\Models;

use App\Helpers\MediaHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PresentCategory extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'image',
        'size',
        'mimetype',
        'available',
    ];

    /**
     * Return category`s presents
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function presents()
    {
        return $this->hasMany(Present::class, 'category_id');
    }

    public function getImageUriAttribute()
    {
        $s3path = MediaHelper::getS3Path(MediaHelper::FILE_TYPE_PRESENT_CATEGORY_IMAGE);

        return MediaHelper::getPublicUrl($s3path . '/' . $this->image);
    }
}
