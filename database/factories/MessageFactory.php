<?php

use Faker\Generator as Faker;

// Message
$factory->define(App\Message::class, function (Faker $faker) {
  return [
    'content'         => $faker->paragraph(5),
  ];
});