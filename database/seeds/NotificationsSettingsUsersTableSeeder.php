<?php

use App\Helpers\NotificationsHelper;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationsSettingsUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();

        foreach ($users as $user) {
            $user->notifications_settings = NotificationsHelper::compareTreeStructure($user);

            $user->save();
        }
    }
}
