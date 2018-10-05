<?php

namespace App\Repositories;

use App\Genre;
use DB;
use Storage;
use Log;

class GenreRepository{

    /**
     * Find all genres with the number of song in each of them.
     *
     * @param  Integer  $id
     * @return Song record
     */
    public function all(){
        return 
            DB::select(
                DB::raw("SELECT genres.id, genres.name, count(albums.genre_id) as total
                            FROM genres
                            LEFT JOIN albums on albums.genre_id=genres.id
                            GROUP BY genres.id, genres.name
                            ORDER BY genres.name ASC"
                ));
    }

    /**
     * Find all genres with published albums only,
     * The number of songs are included in each genre.
     *
     * @param  Integer  $id
     * @return Song record
     */
    public function allPublished(){
        return 
            DB::select(
                DB::raw("SELECT genres.id, genres.name, count(albums.genre_id) as total
                            FROM genres
                            INNER JOIN albums on albums.genre_id=genres.id
                            WHERE albums.published=true
                            GROUP BY genres.id
                            ORDER BY genres.name ASC"
                ));
    }

    /**
     * Make albums from the given genre orphan
     *
     * @param  Integer  $id
     */
    public function setOrphans($id){
        return 
            DB::table('albums')
                ->where('genre_id', $id)
                ->update(['genre_id' => 0]);
    }
}