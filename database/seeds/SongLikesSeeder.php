<?php

use Illuminate\Database\Seeder;
use App\Activity;
use App\Song;
use App\User;

class SongLikesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('likeable_likes')->truncate();
        DB::table('likeable_like_counters')->truncate();

        $total = Song::count();
        $users = User::all();
        
        foreach ($users as $user) {
            // Add 10 to 40 likes per user
            $numberOfLikes = rand(10, 40);

            for ($i = 0; $i < $numberOfLikes; $i++) {
                $song_id = rand(1, $total);
                $song = Song::find($song_id);

                $song->like($user->id);
                $song->total_favorites += 1;
                $song->save();

                // Create activity
                $registered = new Activity([
                    'action'             => 'like',
                    'reference_type'    => 'App\\Song',
                    'reference_id'      => $song->id
                ]);
                $user->activities()->save($registered);
            }
        };
    }
}
