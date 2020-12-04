<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrivateStreamPresent extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'private_stream_id',
        'present_id',
        'price',
    ];

    /**
     * Return user who made a present
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Return present which was made
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function present()
    {
        return $this->belongsTo(Present::class);
    }

    /**
     * Return a private stream to which the gift belongs
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function private_stream()
    {
        return $this->belongsTo(PrivateStream::class);
    }
}
