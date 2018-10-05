<?php

namespace App\Repositories;

use App\User;
use App\Album;
use App\Song;
use DB;
use Storage;
use Log;

class UserRepository{

    /**
     * Find a latests artists that have published an album
     *
     * @param  Integer  $page
     * @return Collection artists
     */
    public function latestAuthors($page = 0){
        $pageSize = 25;

        return 
            DB::select(
                DB::raw("SELECT 
                        users.id, users.name, users.image, count(totals.album_id) as albums,
                        sum(totals.total_songs) as songs, sum(totals.plays) as plays,
                        max(totals.latests_album) as latests_album

                        FROM users
                        INNER JOIN (SELECT albums.user_id,songs.album_id, count(songs.id) as total_songs, 
                                           sum(songs.total_plays) as plays, max(albums.created_at) latests_album 
                                    FROM songs 
                                    INNER JOIN albums on albums.id=songs.album_id
                                    WHERE albums.published=true
                                    GROUP BY albums.user_id, songs.album_id) totals on totals.user_id=users.id
                        GROUP BY users.id, users.name, users.image
                        ORDER BY latests_album DESC
                        LIMIT :page, :pageSize"
                ),[
                'page'      => $page * $pageSize,
                'pageSize'  => $pageSize
            ]);
    }

    /**
     * Count all the authors with a published album, this method
     * is used for pagination only.
     *
     * @return Integer total
     */
    public function countAuthors(){
        $result = DB::select(DB::raw("SELECT count(authors.id) as total
                                    FROM (SELECT users.id
                                    FROM users
                                    inner join albums on albums.user_id=users.id
                                    where albums.published=true
                                    group by users.id) authors"));

        if($result[0]){
            return $result[0]->total;
        }
        return 0;
    }

    /**
     * Find a latests users in the database, it also search by name
     *
     * @param  Integer  $page
     * @param  String   $searchName
     * @return Collection users
     */
    public function searchUsers($page = 0, $searchName){
        $pageSize = 25;

        if($searchName){
            return User::orderBy('created_at','DESC')
                    ->where('name','like','%'.$searchName.'%')
                    ->skip($page * $pageSize)
                    ->take($pageSize)
                    ->get();
        }else{
            return User::orderBy('created_at','DESC')
                    ->skip($page * $pageSize)
                    ->take($pageSize)
                    ->get();
        }
    }

    /**
     * Count the results of the search
     *
     * @param  String  $searchName
     * @return Integer total
     */
    public function countUsers($searchName){
        $pageSize = 25;

        if($searchName){
            return User::where('name','like','%'.$searchName.'%')->count();
        }else{
            return User::count();
        }
    }

    /**
     * Find a latests authors in the database, it also search by name,
     * the search doesn't care if the author has a published album or not
     *
     * @param  Integer  $page
     * @param  String   $searchName
     * @return Collection users
     */
    public function searchAuthors($page = 0, $searchName){
        $pageSize = 25;

        if($searchName){
            return User::orderBy('created_at','DESC')
                    ->where('name','like','%'.$searchName.'%')
                    ->where('author',true)
                    ->skip($page * $pageSize)
                    ->take($pageSize)
                    ->get();
        }else{
            return User::orderBy('created_at','DESC')
                    ->where('author',true)
                    ->skip($page * $pageSize)
                    ->take($pageSize)
                    ->get();
        }
    }

    /**
     * Count the results of the author search
     *
     * @param  String  $searchName
     * @return Integer total
     */
    public function countSearchAuthors($searchName){
        $pageSize = 25;

        if($searchName){
            return User::where('name','like','%'.$searchName.'%')
                        ->where('author',true)
                        ->count();
        }else{
            return User::where('author',true)->count();
        }
    }

    /**
     * Find user by recovery token
     * 
     * @param  String  $token
     */
    public function findUserByRecoveryToken($token) {
        return User::where('recovery_token', $token)->get()->first();
    }
}