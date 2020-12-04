<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrivateStream extends Model
{
    const STATUS_NEW = 1;
    const STATUS_ACCEPTED = 2;
    const STATUS_REJECTED = 3;
    const STATUS_IGNORED = 4;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tariffing',
        'presents',
        'status',
        'started_at',
        'ended_at',
    ];

    /**
     * The attributes that should be converted to Carbon instance.
     *
     * @var array
     */
    protected $dates = [
        'started_at',
        'ended_at',
    ];

    /**
     * Return user who buys private stream
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Return user who sells private stream
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Return presents which belongs to this private stream
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function presents()
    {
        return $this->hasMany(PrivateStreamPresent::class);
    }

    /**
     * Return the messages which belongs to this private stream
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages()
    {
        return $this->hasMany(PrivateStreamMessage::class);
    }
}
