<?php

use Faker\Generator as Faker;

//Thread
$factory->define(App\Activity::class, function (Faker $faker) {
  return [
    'created_at' => $faker->dateTimeBetween($startDate = '-2 months', $endDate = 'now'),
  ];
});
