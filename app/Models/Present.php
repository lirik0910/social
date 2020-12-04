<?php

namespace App\Models;

use App\Helpers\MediaHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Present extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'image',
        'mimetype',
        'size',
        'price'
    ];

    /**
     * Return media presents
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function media_presents()
    {
        return $this->hasMany(MediaPresent::class);
    }

    /**
     * Return private stream presents
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function private_stream_presents()
    {
        return $this->hasMany(PrivateStreamPresent::class);
    }

    /**
     * Return present`s category
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(PresentCategory::class, 'category_id');
    }

    public function getImageUriAttribute()
    {
        $s3path = MediaHelper::getS3Path(MediaHelper::FILE_TYPE_PRESENT_IMAGE);

        return MediaHelper::getPublicUrl($s3path . '/' . $this->category_id . '/'. $this->image);
    }
}
