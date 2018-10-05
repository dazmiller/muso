<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Seeding the database, this operation may take a while.');
        Model::unguard();
        

        if(file_exists(storage_path().'/app/public/users/')){
            $this->delTree(storage_path().'/app/public/users/');
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        DB::table('files')->truncate();
        DB::table('activities')->truncate();
        
        $this->call([
            UsersTableSeeder::class,
            PostsTableSeeder::class,
            GenresTableSeeder::class,
            AlbumsTableSeeder::class,
            SongLikesSeeder::class,
            SongPlaysSeeder::class,
        ]);
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

        Model::reguard();
        $this->command->info('Seeding Completed!');
    }

    public function delTree($dir) {
       $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
          (is_dir("$dir/$file")) ? $this->delTree("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }
}
