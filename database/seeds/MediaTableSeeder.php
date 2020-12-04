<?php

use Illuminate\Database\Seeder;
use App\Models\Media;

class MediaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $medias = [
            'RNLe7p8JwNyWKByZPy8txWeOmcfDpv.jpg',
            'mTXpRZrHtZNeJqJ6wVSH73neGVL79g.jpg',
            'Xc7F6EJhac0KMPwpDczWUIygndjuBr.jpg',
            'WdXi7U0AFKHGkeOmcJnivIYxWJfq4z.jpg',
        ];

        for ($k = 0; $k < 200; $k++) {
            Media::create([
                'user_id' => rand(1, 2),
                'type' => Media::TYPE_IMAGE,
                'name' => array_rand(array_flip($medias)),
                'description' => 'Some shit with #tags',
                'mimetype' => 'image/jpeg',
                'size' => rand(300, 10000)
            ]);
        }
    }
}
