<?php

namespace App\Helpers;


use App\Events\ActiveChatEvent;
use App\Models\Meeting;
use App\Models\UsersPrivateChatRoom;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;


class BlockHelper
{
    public static function checkCurrentEventsExists($current_user, $other_user)
    {
        return DB
            ::query()
            ->select(DB::raw(1))
            ->orWhereExists(function ($query) use ($current_user, $other_user) {
                $query
                    ->select(DB::raw(1))
                    ->from('auctions')
                    ->where('user_id', '=', $current_user->id)
                    ->where('last_bid_user_id', '=', $other_user->id)
                    ->where('end_at', '>', Carbon::now())
                    ->whereNull('cancelled_at');
            })
            ->orWhereExists(function ($query) use ($current_user, $other_user) {
                $query
                    ->select(DB::raw(1))
                    ->from('adverts')
                    ->where('user_id', '=', $current_user->id)
                    ->where('respond_user_id', '=', $other_user->id)
                    ->where('end_at', '>', Carbon::now())
                    ->whereNull('cancelled_at');
            })
            ->orWhereExists(function ($query) use ($current_user, $other_user) {
                $query
                    ->select(DB::raw(1))
                    ->from('meetings')
                    ->where(function ($q) use ($current_user, $other_user) {
                        $q->where(function ($q) use ($current_user, $other_user) {
                            $q
                                ->where('seller_id', $other_user->id)
                                ->where('user_id', $current_user->id);
                        })->orWhere(function ($q) use ($current_user, $other_user) {
                            $q
                                ->where('seller_id', $current_user->id)
                                ->where('user_id', $other_user->id);
                        });
                    })
                    ->where(function ($q) {
                        $q->where('status', '=', Meeting::STATUS_ACCEPTED);
                        $q->orWhere('status', '=', Meeting::STATUS_NEW);
                    });
            })
            ->exists();
    }

    /**
     * Update chat rooms for this users
     *
     * @param $user
     * @param $blocked_user
     */
    public static function blockChatRooms($user, $blocked_user)
    {
        $rooms = UsersPrivateChatRoom
            ::where(function ($q) use ($user, $blocked_user) {
                $q->where('user_id', $user->id);
                $q->where('seller_id', $blocked_user->id);
            })
            ->orWhere(function ($q) use ($user, $blocked_user) {
                $q->where('user_id', $blocked_user->id);
                $q->where('seller_id', $user->id);
            })
            ->get();

        $rooms_ids = $rooms
            ->pluck('id')
            ->toArray();

        UsersPrivateChatRoom
            ::whereIn('id', $rooms_ids)
            ->update([
                'ended_at' => Carbon::now(),
                'ended_by_id' => $user->id,
            ]);

        $users = collect([$user, $blocked_user]);

        foreach ($rooms as $room) {
            $room->refresh();

            foreach ($users as $user) {
                $receiver = $users->where('id', '!=', $user->id)->first();

                if (!empty($receiver)) {
                    $data = ChatRoomHelper::formatData(ChatRoomHelper::CHAT_ROOM_EVENT_ENDED, $user, $room);

                    event(new ActiveChatEvent($receiver->id, $data));
                }
            }
        }
    }
}
