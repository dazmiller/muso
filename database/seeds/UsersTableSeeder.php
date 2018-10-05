<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Activity;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       // DB::table('comments')->truncate();
        //DB::table('activities')->truncate();
       // DB::table('users')->truncate();

        factory(User::class, 110)
            ->create()
            ->each(function($user) {
                $registered = new Activity([
                    'action'             => 'signup',
                    'reference_type'    => 'App\\User',
                    'reference_id'      => $user->id
                ]);
                $user->activities()->save($registered);
            });;
    }
}
