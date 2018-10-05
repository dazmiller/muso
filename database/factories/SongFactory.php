<?php

use Faker\Generator as Faker;

//Songs
$factory->define(App\Song::class, function (Faker $faker) {
  return [
    'title'         => 'Default value',
    'description'   => $faker->paragraph(5),
    'lyric'         => $faker->paragraphs(8, true),
    'total_plays'   => 0,
    'total_favorites'=> 0,
    'duration'      => '00:00',
    'created_at'    => $faker->dateTimeThisYear(),
  ];
});