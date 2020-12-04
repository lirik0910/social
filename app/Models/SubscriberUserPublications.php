<?php

namespace App\Models;

use App\Traits\ReflectionTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SubscriberUserPublications extends Model
{
    use ReflectionTrait;

    const PUB_TYPE_AUCTIONS = 'auctions';
    const PUB_TYPE_ADVERTS = 'adverts';
    const PUB_TYPE_MEDIA = 'media';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subscriber_id',
        'owner_id',
        'pub_type',
        'pub_id'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subscriber()
    {
        return $this->belongsTo(User::class, 'subscriber_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function pub() : MorphTo
    {
        return $this->morphTo();
    }
}
