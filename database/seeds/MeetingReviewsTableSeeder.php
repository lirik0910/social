<?php

use Illuminate\Database\Seeder;

class MeetingReviewsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i = 0; $i < 10; $i++) {
            \App\Models\MeetingReview::create([
                'user_id' => 5,
                'meeting_id' => 3,
                'value' => (rand(10, 50) / 10),
                'description' => Str::random(15)
            ]);
        }
    }
}
