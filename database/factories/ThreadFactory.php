<?php

use Faker\Generator as Faker;

//Thread
$factory->define(App\Thread::class, function (Faker $faker) {
  return [
    'title'         => $faker->sentence,
  ];
});