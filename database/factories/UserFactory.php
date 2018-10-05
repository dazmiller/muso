<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\User::class, function (Faker $faker) {
    if(rand(1,2) == 1){
        $gender = 'Male';
        $name = $faker->firstNameMale().' '.$faker->lastName;
        $image = 'https://randomuser.me/api/portraits/men/'.rand(1,90).'.jpg';
    }else{
        $gender = 'Female';
        $name = $faker->firstNameFemale().' '.$faker->lastName;
        $image = 'https://randomuser.me/api/portraits/women/'.rand(1,90).'.jpg';
    }
    return [
        'name'      => $name,
        'email'     => $faker->unique()->safeEmail,
        'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        'country'   => $faker->country,
        'postcode'  => $faker->postcode,
        'latitude'  => $faker->latitude,
        'longitude' => $faker->longitude,
        'about'     => $faker->paragraph(3),
        'gender'    => $gender,
        'image'     => $image,
        'dob'       => $faker->dateTimeThisCentury($max = 'now')->format('Y-m-d'),
        'occupation'=> $faker->catchPhrase,
        'website'   => 'http://testing.com',
    ];

    // return [
    //     'name' => $faker->name,
    //     'email' => $faker->unique()->safeEmail,
    //     'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
    //     'remember_token' => str_random(10),
    // ];
});
