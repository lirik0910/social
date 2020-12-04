<?php

use Illuminate\Database\Seeder;

class PublicStreamViewsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $public_streams = \App\Models\PublicStream::all();
        $user_ids = collect([['id' => 1], ['id' => 2], ['id' => 4], ['id' => 5]]);

        $public_streams->map(function ($item) use ($user_ids) {
            for ($k = 0; $k < rand(1, 10); ++$k) {
                \App\Models\PublicStreamView::create([
                    'public_stream_id' => $item->id,
                    'user_id' => $user_ids->where('id', '!=', $item->user_id)->random()['id']
                ]);
            }
        });
    }
}
