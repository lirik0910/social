<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedViewed extends Model
{
    protected $table = 'feed_viewed';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'media_ids',
        'auctions_ids',
        'adverts_ids',
    ];

    /**
     * Return user who watched this content on feed page
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
