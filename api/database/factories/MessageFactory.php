<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\message;
use Faker\Generator as Faker;

$factory->define(message::class, function (Faker $faker) {
    return [
        'type' => rand(0, 5),
        'text' => Str::random(300)
    ];
});
