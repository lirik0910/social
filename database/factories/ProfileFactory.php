<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Profile;
use Faker\Generator as Faker;

$factory->define(Profile::class, function (Faker $faker) {
    return [
        'age' => $faker->date($format = 'Y-m-d', $max = 'now'),
        'sex' => $faker->numberBetween($min = 1, $max = 2),
        'dating_preference' => $faker->numberBetween($min = 1, $max = 3),

        'country' => $faker->country,
        'region' => $faker->state,
        'address' => $faker->address,
        'lat' => $faker->latitude($min = -90, $max = 90),
        'lng' => $faker->longitude($min = -180, $max = 180),

        'name' => $faker->firstName,
        'surname' => $faker->lastName,

        'height' => $faker->numberBetween($min = 120, $max = 250),
        'physique' => $faker->numberBetween($min = 1, $max = 6),
        'appearance' => $faker->numberBetween($min = 1, $max = 5),
        'eye_color' => $faker->numberBetween($min = 1, $max = 5),
        'hair_color' => $faker->numberBetween($min = 1, $max = 6),
//        'occupation' => $faker->numberBetween($min = 1, $max = 3),-
        'marital_status' => $faker->numberBetween($min = 1, $max = 6),
        'kids' => $faker->numberBetween($min = 0, $max = 1),
        'languages' => $faker->languageCode,
        'smoking' => $faker->numberBetween($min = 1, $max = 3),
        'alcohol' => $faker->numberBetween($min = 1, $max = 3),
        'about' => $faker->paragraph($nbSentences = 3, $variableNbSentences = true),

    ];
});
