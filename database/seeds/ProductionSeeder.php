<?php

use Illuminate\Database\Seeder;
use App\Activity;
use App\User;

class ProductionSeeder extends Seeder
{
    /**
     * Run the database seeds for production only.
     * Creates the admin user.
     *
     * @return void
     */
    public function run()
    {
        
        $user = new User();
        $user->name     = 'Administrator';
        $user->email    = 'admin@admin.com';
        $user->password = bcrypt('admin123');
        $user->occupation = 'Platform Administrator';
        $user->image    = 'https://randomuser.me/api/portraits/men/32.jpg';
        $user->admin    = true;
        $user->author   = true;

        $user->save();

        $registered = new Activity([
            'action'            => 'signup',
            'reference_type'    => 'App\\User',
            'reference_id'      => $user->id
        ]);

        $user->activities()->save($registered);
    }
}
