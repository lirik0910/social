<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Auction;
use Faker\Generator as Faker;

$factory->define(Auction::class, function (Faker $faker) {
    return [
        'location_lat' => $faker->latitude($min = -90, $max = 90),
        'location_lng' => $faker->longitude($min = -180, $max = 180),
        'meeting_date' => $faker->dateTimeInInterval($startDate = 'now', $interval = '+ 30 days', $timezone = null) ,
        'input_bid' => $faker->numberBetween($min = 1, $max = 10),
        'minimal_step' => $faker->numberBetween($min = 1, $max = 10),
        'min_age' => $faker->numberBetween($min = 18, $max = 24),
        'max_age' => $faker->numberBetween($min = 24, $max = 60),
        'description' => $faker->paragraph($nbSentences = 3, $variableNbSentences = true),
        'outfit' => $faker->numberBetween($min = 0, $max = 1),
        'participants' => $faker->numberBetween($min = 0, $max = 21),
        'photo_verified_only' => $faker->numberBetween($min = 0, $max = 1),
        'fully_verified_only' => $faker->numberBetween($min = 0, $max = 1),
        'location_for_winner_only' => $faker->numberBetween($min = 0, $max = 1),
        'created_at' => $faker->dateTimeBetween($startDate = '-30 days', $endDate = '-5 day', $timezone = null),
        'end_at' => $faker->dateTimeBetween($startDate = '-5 days', $endDate = '+5 days', $timezone = null),
    ];
});
