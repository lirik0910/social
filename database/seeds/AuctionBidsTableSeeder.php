<?php

use Illuminate\Database\Seeder;

class AuctionBidsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $auctions = \App\Models\Auction::all();
        $user_ids = collect([['id' => 1], ['id' => 2], ['id' => 4], ['id' => 5]]);

        $auctions->map(function ($item) use ($user_ids) {
            for ($k = 0; $k < rand(1, 10); ++$k) {
                \App\Models\AuctionBid::create([
                    'auction_id' => $item->id,
                    'user_id' => $user_ids->where('id', '!=', $item->user_id)->random()['id'],
                    'value' => rand($item->minimal_step, $item->input_bid)
                ]);
            }
        });
    }
}
