<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\chat;
use Faker\Generator as Faker;

$factory->define(chat::class, function (Faker $faker) {
    $type = rand(0,3);
    return [
        "type" => $type,
        "title" => $type !== chat::ONE2ONE ? Str::random(40) : null,
        "desc" => ( rand(0,5) % 2 == 0 and $type !== chat::ONE2ONE ) ? Str::random(100) : null,
        "avatar" => ( rand(0,5) % 2 == 0 and $type !== chat::ONE2ONE ) ? Str::random(40) : null,
        "link" => $type !== chat::ONE2ONE ? Str::random(40) : null,
        
    ];
});
