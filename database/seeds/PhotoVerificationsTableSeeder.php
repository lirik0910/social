<?php

use Illuminate\Database\Seeder;
use App\Models\PhotoVerification;

class PhotoVerificationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i = 0; $i < 10; $i++) {
            PhotoVerification::create([
                'name' => 'image_name.jpeg',
                'mimetype' => 'image/jpeg',
                'size' => rand(100, 3000),
            ]);
        }
    }
}
