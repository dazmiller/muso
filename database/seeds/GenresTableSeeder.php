<?php

use Illuminate\Database\Seeder;
use App\Genre;

class GenresTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('genres')->truncate();
        $genres = [
            'Avant-Garde',
            'Blues',
            'Children\'s',
            'Classical',
            'Comedy/Spoken',
            'Country',
            'Easy Listening',
            'Electronic',
            'Folk',
            'Holiday',
            'International',
            'Jazz',
            'Latin',
            'New Age',
            'Pop/Rock',
            'R&B',
            'Rap',
            'Reggae',
            'Religious',
            'Stage & Screen',
            'Vocal',
        ];

        for ($i = 0; $i < 20; $i++) {
            $genre = new Genre();
            $genre->name = $genres[$i];

            $genre->save();
        }
    }
}
