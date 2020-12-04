<?php

use Illuminate\Database\Seeder;

class PublicStreamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($k = 0; $k < 100; $k++) {
            \App\Models\PublicStream::create([
                'user_id' => array_rand(array_flip([1, 2, 4, 5])),
                'title' => 'Random title №' . $k,
                'description' => 'Description gg',
                'tariffing' => rand(0, 50),
                'message_cost' => rand(0, 100),
                'min_age' => rand(14, 30),
                'max_age' => rand(31, 60),
                'for_subscribers_only' => true,
                'started_at' => now(),
                'current_views' => rand(0, 300)
            ]);
        }
    }
}
