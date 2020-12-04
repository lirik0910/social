<?php

namespace App\Models;

use App\Helpers\MediaHelper;
use App\Traits\ThumbsTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PublicStream extends Model
{
    use ThumbsTrait;

    /**
     * The attributes that are mass assignable.

     * @var array
     */
    protected $fillable = [
        'preview',
        'title',
        'description',
        'tariffing',
        'message_cost',
        'min_age',
        'max_age',
        'for_subscribers_only',
        'planned_at',
        'started_at',
        'ended_at',
        'current_views',
    ];

    /**
     * The attributes that should be converted to Carbon instance.

     * @var array
     */
    protected $dates = [
        'planned_at',
        'started_at',
        'ended_at'
    ];

    /**
     * Return user associated with profile

     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

     /**
     * Return current views for public stream
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function views()
    {
        return $this->hasMany(PublicStreamView::class);
    }

    /**
     * Return reports for this public stream
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function reports()
    {
        return $this->morphMany(Report::class, 'reported');
    }

    /**
     * Return users who subscribes on this public stream
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function subscribers()
    {
        return $this->belongsToMany(User::class, 'public_stream_subscribes', 'public_stream_id', 'user_id');
    }

    /**
     * Get preview uri attribute
     *
     * @return string
     */
    public function getPreviewUriAttribute()
    {
        $user = Auth::user();

        if($this->preview) {
            $uri = MediaHelper::getPublicUrl(Media::STREAM_BUCKET_ROOT_PATH . '/' . $this->user_id . '/' . $this->preview);
        } else {
            $uri = MediaHelper::getPublicUrl(Media::AVATAR_BUCKET_ROOT_PATH . '/' . $this->user_id .'/' . $user->image);
        }
        return $uri;
    }
}
