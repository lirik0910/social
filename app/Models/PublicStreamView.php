<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicStreamView extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
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
        'ended_at'
    ];

    /**
     * Return user who made view
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Return public stream which associated with view
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function public_stream()
    {
        return $this->belongsTo(PublicStream::class);
    }
}
