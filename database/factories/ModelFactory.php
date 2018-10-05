<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/


//Albums
$factory->define(App\Album::class, function (Faker\Generator $faker) {
    return [
        'title'         => $faker->catchPhrase,
        'description'   => $faker->paragraph(5),
        'release_date'  => $faker->dateTimeThisCentury()->format('Y-m-d'),
        'published'     => true,
        'created_at'    => $faker->dateTimeThisYear(),
        'duration'      => '00:00',
    ];
});

//Genre
// $factory->define(App\Genre::class, function (Faker\Generator $faker) {
//     return [
//         'name'         => $faker->catchPhrase
//     ];
// });


//posts
$factory->define(App\Post::class, function (Faker\Generator $faker) {
    return [
        'title'             => $faker->catchPhrase,
        'content'           => $faker->paragraphs(10, true),
        'allow_comments'    => true,
        'published'         => true,
        'created_at'        => $faker->dateTimeThisYear()
    ];
});