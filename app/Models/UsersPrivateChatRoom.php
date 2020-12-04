<?php

namespace App\Models;

use App\Traits\ReflectionTrait;
use Illuminate\Database\Eloquent\Model;

class UsersPrivateChatRoom extends Model
{
    use ReflectionTrait;

    const VIEW_TYPE_ALL = 0;
    const VIEW_TYPE_AUTHOR = 1;
    const VIEW_TYPE_PARTICIPANT = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'status',
        'price',
        'is_blocked'
    ];

    /**
     * @return array
     */
    public function toArray()
    {
        $array = parent::toArray();
        $array['unread'] = $this->unread;
        $array['diff'] = $this->diff;

        return $array;
    }

    /**
     * Return last message for this chat room
     *
     * @return Model|\Illuminate\Database\Eloquent\Relations\HasOne|object|null
     */
    public function getLastMessageAttribute()
    {
        return UsersPrivateChatRoomMessage::where('room_id', $this->id)->orderBy('created_at', 'desc')->first();
    }

    /**
     * Return the user who created chat
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Return the user who earn money for chat
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function reports()
    {
        return $this->morphMany(Report::class, 'reported');
    }

    /**
     * Return unread messages for authorized user in this chat room
     *
     * @return integer
     */
    public function getUnreadAttribute()
    {
        $user_id = \Auth::user()->id;

        return UsersPrivateChatRoomMessage
            ::where([
                'room_id' => $this->id,
                'status' => false,
            ])
            ->where('user_id', '!=', $user_id)
            ->count();
    }
}
