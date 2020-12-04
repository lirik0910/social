<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use App\Models\Auction;
use App\Models\Meeting;

class AuctionTableSeeder extends Seeder
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

        if($users->count() > 1) {
            for($i = 0; $i < 120; $i++) {
                $iter_user = $users->random();
                $rand_days = rand(3, 10);
                $inputBid = rand(200, 2000);
                $hasBid = Arr::random([false, true, true, true]);

                $auction = Auction::create([
                    'user_id' => $iter_user->id,
                    'location_lat' => rand(-90, 90),
                    'location_lng' => rand(-180, 180),
                    'meeting_date' => Carbon::now()->addDays($rand_days),
                    'input_bid' => $inputBid,
                    'minimal_step' => rand(10, 200),
                    'min_age' => rand(16, 25),
                    'max_age' => rand(35, 50),
                    'photo_verified_only' => Arr::random([true, false]),
                    'outfit' => Arr::random(array_keys(Meeting::availableParams('outfit'))),
                    'end_at' => Carbon::now()->addDays($rand_days - 1),
                    'status' => Arr::random(array_keys(Auction::availableParams('status'))),
                    'address' => \Illuminate\Support\Str::random(16),
                    'city' => \Illuminate\Support\Str::random(16),
                    'location_for_winner_only' => Arr::random([true, false]),
                    'participants' => $hasBid ? 1 : 0,
                ]);

                if($hasBid) {
                    $bid = new \App\Models\AuctionBid([
                        'value' => rand($inputBid, 40000),
                        'user_id' => $users->except([$iter_user->id])->random()->id,
//                        'user_id' => $users->reject(function ($user) use($iter_user){
//                            return $user->id !== $iter_user->id;
//                        })->random()->id,
                    ]);

                    $auction->bids()->save($bid);
                    $auction->last_bid_id = $bid->id;
                    $auction->save();
                }
            }
        }
    }
}
