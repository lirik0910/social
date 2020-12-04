<?php

use Illuminate\Database\Seeder;

class SlugsUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = \App\Models\User::all();

        foreach($users as $user) {
            if(!$user->slug) {
                $user->slug = $user->generateSlug();

                $user->save();
            }
        }
    }
}
