<?php


namespace App\Libraries\GraphQL\User;


use App\Helpers\NotificationsHelper;
use Illuminate\Support\Facades\Auth;

class NotificationsSettingsCreate
{
    public function resolve()
    {
        $user = Auth::user();

        $default_notifications_settings = NotificationsHelper::getNotificationsTree();

        return $user->update(['notifications_settings' => $default_notifications_settings]);
    }
}
