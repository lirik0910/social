<?php

namespace App\Observers;

use App\Helpers\ChatRoomHelper;
use App\Models\BlockedUser;
use App\Models\Subscribe;
use App\Models\UsersPrivateChatRoom;
use Carbon\Carbon;

class  BlockedUserObserver
{
    /**
     * Handle the created eloquent model event
     *
     * @param BlockedUser $blocked_user_record
     */
    public function created(BlockedUser $blocked_user_record)
    {
        $this->unsubscribeUsers($blocked_user_record);
    }

    /**
     * Handle the models on "deleted" event.
     *
     * @param BlockedUser $blocked_user_record
     * @return void
     */
    public function deleted(BlockedUser $blocked_user_record)
    {
        //
    }

    /**
     * Handle the restored eloquent model event
     *
     * @param BlockedUser $blocked_user_record
     */
    public function restored(BlockedUser $blocked_user_record)
    {
        $this->unsubscribeUsers($blocked_user_record);
    }

    /**
     * Unsubscribe users by each other
     *
     * @param $blocked_user_record
     * @return void
     */
    protected function unsubscribeUsers($blocked_user_record)
    {
        if ($blocked_user_record->user && $blocked_user_record->blocked_user){
            $this->deleteSubscriptions($blocked_user_record->user, $blocked_user_record->blocked_user);
            $this->deleteSubscriptions($blocked_user_record->blocked_user, $blocked_user_record->user);
        }
    }

    /**
     * @param $owner
     * @param $subscriber
     */
    protected function deleteSubscriptions($owner,$subscriber)
    {
        $unsubscribe = Subscribe
            ::where([
                ['user_id', '=', $owner->id],
                ['subscriber_id', '=', $subscriber->id]
            ])
            ->delete();
        if (!empty($unsubscribe)) {
            $owner->decrement('subscribers_count');
            $subscriber->decrement('subscribes_count');
        }
    }
}
