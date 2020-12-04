<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrivateStreamMessage extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'private_stream_id',
        'recipient_id',
        'body',
    ];

    /**
     * Return the user who send message
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Return the user who received the message
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    /**
     * Return a private stream to which the message belongs
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function private_stream()
    {
        return $this->belongsTo(PrivateStream::class);
    }
}
