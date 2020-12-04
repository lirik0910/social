<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

use App\Models\UsersPrivateChatRoom;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('rooms.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
    // TODO ADD ADMINISTRATOR TO CONDITION
});

Broadcast::channel('profile.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('call.{id}', function ($user) {
    return true;
});

Broadcast::channel('edited_room.{id}', function ($user, $id) {
    $room = UsersPrivateChatRoom::where('id', (int) $id)->firstOrFail();

    return $user->can('view', $room);
});

Broadcast::channel('balance_info.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Favorites
// general channel for listening to users online and posting posts for subscribers
Broadcast::channel('presence', function ($user) {
    if($user){
        return ['id' => $user->id, 'name' => $user->name];
    }
});

Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
// End Favorites


// Support
Broadcast::channel('support_message_receiver.{id}', function ($user, $id) {
    //$support = \App\Models\Support::whereId($id)->first();

    return $user->id == $id;
});

Broadcast::channel('changed_support.{id}', function ($user, $id) {
    $support = \App\Models\Support::whereId($id)->first();

    return !empty($support) && $support->user_id == $user->id;
});
// End Support

Broadcast::channel('updated_auction.{id}', function ($user, $id) {
   return true;
});
