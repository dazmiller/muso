<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Activity;
use App\Song;
use App\User;

class SongPlaysSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $total = Song::count();
        $users = User::all();
        $startDate = Carbon::now()->subMonth()->timestamp;
        $now = Carbon::now()->timestamp;

        foreach ($users as $user) {
            $plays = rand(50, 100);

            // One user plays 50 to 100 random songs
            for ($i=0; $i < $plays; $i++) {
                $song_id = rand(1, $total);
                $song = Song::find($song_id);
                $song->total_plays = $song->total_plays + 1;
                $song->save();

                $int= rand($startDate, $now);
                $randomDate = date("Y-m-d H:i:s", $int);
                $activity = new Activity();
                $activity->fill([
                    'action'    => 'play',
                    'user_id'   => $user->id,
                    'reference_type'    => 'App\\Song',
                    'reference_id'      => $song->id,
                ]);
                $activity->created_at = $randomDate;
                $activity->updated_at = $randomDate;
                $activity->save();
            }
        }
    }
}
