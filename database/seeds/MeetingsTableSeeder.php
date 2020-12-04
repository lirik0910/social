<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use App\Models\Meeting;


class MeetingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws ReflectionException
     */
    public function run()
    {
        $users = \App\Models\User::all();
        $charities = \App\Models\CharityOrganization::all();

        if($users->count() > 1) {
            for($i = 0; $i < 100; $i++) {
                $iter_users = $users->shuffle()->take(2);
                $charity_id = count($charities) ? $charities->random()->id : null;

                Meeting::create([
                    'user_id' => $iter_users->first()->id,
                    'seller_id' => $iter_users->last()->id,
                    'location_lat' => rand(-90, 90),
                    'location_lng' => rand(-180, 180),
                    'meeting_date' => Carbon::now()->addDays(3),
                    'price' => rand(100, 2000),
                    'outfit' => Arr::random(array_keys(Meeting::availableParams('outfit'))),
                    'address' => 'London, Baker str., 21B',
                    'safe_deal' => Arr::random([true, false]),
                    'charity_organization_id' => Arr::random([$charity_id, null]),
                    'status' => Arr::random(array_keys(Meeting::availableParams('status'))),
                  //  'address' => \Illuminate\Support\Str::random(16)
                ]);
            }
        }
    }
}
