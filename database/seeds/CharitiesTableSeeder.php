<?php

use Illuminate\Database\Seeder;

class CharitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Models\CharityOrganization::class, 64)->create();
    }
}
