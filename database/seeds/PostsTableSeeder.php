<?php

use Illuminate\Database\Seeder;
use App\Post;
use App\User;
use App\File as FileEntry;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Log;

class PostsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('posts')->truncate();


        $authors = User::take(20)->get();

        foreach ($authors as $author) {
            for($i=0;$i<5;$i++){
                $post = $author->posts()->save(factory(Post::class)->make());

                $imagePath = 'users/'.$author->id.'/posts/'.$post->id;
                $name = rand(1, 16);
                mkdir(storage_path().'/app/public/'.$imagePath, 0777, true);
                copy(storage_path().'/seed/posts-art/'.$name.'.jpeg', storage_path().'/app/public/'.$imagePath.'/'.$name.'.jpeg');

                $asset = new FileEntry();
                $asset->fill([
                    'id'            => Uuid::uuid1(),
                    'fileable_type' => 'App\\Post',
                    'fileable_id'   => $post->id,
                    'name'          => $name.'.jpeg',
                    'original_name' => $name.'.jpeg',
                    'content_type'  => 'image/jpeg',
                    'size'          => filesize(storage_path().'/seed/posts-art/'.$name.'.jpeg'),
                    'path'          => $imagePath,
                ]);
                $asset->save();
            }
        }
    }
}
