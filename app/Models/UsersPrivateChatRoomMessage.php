<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class UsersPrivateChatRoomMessage extends Model
{
    const TYPE_MESSAGE = 1;
    const TYPE_PRICE_CHANGED = 2;

    protected $connection = 'mongodb';
    protected $collection = 'chat_messages';
    protected $fillable = [
        'room_id',
        'user_id',
        'nickname',
        'avatar',
        'message',
        'price',
        'status'
    ];

    public function toArray()
    {
        $array = parent::toArray();
        $array['diff'] = $this->diff;
        return $array;
    }
}
