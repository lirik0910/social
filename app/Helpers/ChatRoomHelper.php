<?php

namespace App\Helpers;

use App\Models\UsersPrivateChatRoom;
use App\Events\ActiveChatEvent;
use App\Traits\ReflectionTrait;

class ChatRoomHelper
{
    use ReflectionTrait;

    const CHAT_ROOM_EVENT_CLOSED = 1;
    const CHAT_ROOM_EVENT_CREATED = 2;
    const CHAT_ROOM_EVENT_READED = 3;
    const CHAT_ROOM_EVENT_ENDED = 4;
    const CHAT_ROOM_EVENT_MESSAGE_CREATED = 5;
    const CHAT_ROOM_EVENT_MESSAGE_READED = 6;

    /**
     * @return \App\Models\User|null
     */
    public static function user()
    {
        return \Auth::user();
    }

    /**
     * @param $status
     * @param $room
     */
    public static function activeChatRoomEventRoom($status, $room)
    {
        $user = (self::user()) ? self::user() : $room->user;

        $receiver_user_id = self::getActiveRoomUserId($room, $user);
        $data = self::formatData($status, $room->user, $room);

        event(new ActiveChatEvent($receiver_user_id, $data));
    }

    /**
     * @param $status
     * @param null $msg
     * @throws \App\Exceptions\GraphQLLogicRestrictException
     */
    public static function activeChatRoomEvent($status, $msg = null)
    {
        if ($msg) {
            $msg = [
                "id" => strval($msg->id),
                "room_id" => strval($msg->room_id),
                "user_id" => strval($msg->user_id),
                "type" => $msg->type,
                "message" => $msg->message,
                "status" => $msg->status,
                "price" => $msg->price,
                "created_at" => $msg->created_at->toIso8601String(),
                "updated_at" => $msg->created_at->toIso8601String(),
            ];
        }

        $room = UsersPrivateChatRoom::where('id', $msg['room_id'])->firstOrFail();

        $user = (self::user()) ? self::user() : $room->user;

        broadcast(
            new ActiveChatEvent(
                self::getActiveRoomUserId($room, $user),
                self::formatData($status, (self::user()) ? self::user() : $room->user, $room, $msg)
            ))->toOthers();
    }

    /**
     * @param $room
     * @param $user
     * @return integer
     */
    public static function getActiveRoomUserId($room, $user)
    {
        // get subscriber & create event with updated rooms data
        return $room->user_id != $user->id
            ? $room->user_id
            : $room->seller_id;
    }

    /**
     * @param $status
     * @param $user
     * @param $room
     * @param $msg
     * @return array
     */
    public static function formatData($status, $user, $room, $msg = null)
    {
        $user_data = !empty($user)
            ? [
                "id" => strval($user->id),
                "nickname" => $user->nickname,
                "flags" => $user->flags,
                "slug" => $user->slug,
                "avatar_thumbs" => $user->getThumbs($user, ["sizes" => ["320"]]),
              ]
            : null;

        $data = [
            'status' => $status,
            'room_id' => $room->id,
            'room_diff' => $room->diff,
            'user' => $user_data
        ];

        if (!empty($msg)) {
            $data['message'] = $msg;
        }

        if ($status === self::CHAT_ROOM_EVENT_ENDED) {
            $data['room_ended_at'] = $room->ended_at;
        }

        return $data;
    }
}
