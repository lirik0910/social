<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CharityOrganization;
use Faker\Generator as Faker;

$factory->define(CharityOrganization::class, function (Faker $faker) {
    return [
        'image' => 'https://www.eyes-down.net/workspace/uploads/images/services-images/ed-charity-graphic-white_1.gif',
        'name' => $faker->text($maxNbChars = 25),
        'description' => $faker->paragraph($nbSentences = 3, $variableNbSentences = true),
        'link' => 'https://wellcome.ac.uk',
//        'payment_receiver_name',
//        'payment_receiver_address',
//        'payment_receiver_bank',
//        'payment_receiver_bank_address',
//        'payment_receiver_bank_account'
    ];
});
