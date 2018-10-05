<?php

namespace App\Repositories;

use App\Post;
use DB;
use Config;
use Storage;
use Log;

class PostRepository{

    /**
     * Find all posts for administration porpuses
     *
     * @param  Map  $options
     * @return Collection posts
     */
    public function getAllPosts($options){
        $pageSize = Config::get('app.page_size');

        $query = Post::select(DB::raw("
                        posts.id,posts.title,posts.content,posts.allow_comments,posts.published,posts.created_at,
                        users.id as user_id,users.name, users.image
                    "))
                    ->join('users','users.id','=','posts.author_id')
                    ->orderBy('posts.created_at','DESC')
                    ->skip($options['page'] * $pageSize)
                    ->take($pageSize);

        if($options['search']){
            $query->where('posts.title','like','%'.$options['search'].'%');
        }

        if($options['drafts']){
            $query->where('posts.published',false);
        }

        return $query->get();
    }

    /**
     * Count all posts for administration porpuses
     *
     * @param  Map  $page
     * @return Collection posts
     */
    public function countAllPosts($options){
        $query = Post::select('id');

        if($options['search']){
            $query->where('posts.title','like','%'.$options['search'].'%');
        }

        if($options['drafts']){
            $query->where('posts.published',false);
        }

        return $query->count();
    }

    /**
     * Find the latest published posts
     *
     * @param  Integer  $page
     * @return Collection posts
     */
    public function getLatestsPublished($page = 0){
        $pageSize = Config::get('app.page_size');

        return Post::select(DB::raw("
                        posts.id,posts.title,posts.content,posts.allow_comments,posts.created_at,
                        users.id as user_id,users.name, users.image,
                        assets.id as asset_id,assets.content_type,assets.name as asset_name, assets.path as asset_path, assets.public as asset_public
                    "))
                    ->join('users','users.id','=','posts.author_id')
                    ->leftJoin(DB::raw("(select * from files where files.fileable_type='App\\\\Post') assets"),'assets.fileable_id','=','posts.id')
                    ->where('posts.published',true)
                    ->orderBy('posts.created_at','DESC')
                    ->skip($page * $pageSize)
                    ->take($pageSize)
                    ->get();
    }

    public function countPublishedPosts() {
        return Post::where('posts.published', true)->count();
    }

    public function findPublishedPost($id) {
      return Post::select(DB::raw("
                      posts.id,posts.title,posts.content,posts.allow_comments,posts.created_at,
                      users.id as user_id,users.name, users.image, users.about,
                      assets.id as asset_id,assets.content_type,assets.name as asset_name, assets.path as asset_path, assets.public as asset_public
                  "))
                  ->join('users','users.id','=','posts.author_id')
                  ->leftJoin(DB::raw("(select * from files where files.fileable_type='App\\\\Post') assets"),'assets.fileable_id','=','posts.id')
                  ->where('posts.published', true)
                  ->where('posts.id', $id)
                  ->first();
    }

}
