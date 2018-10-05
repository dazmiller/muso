<?php

namespace App\Repositories;

use App\User;
use App\Album;
use App\Song;
use App\Genre;
use Carbon\Carbon;
use DB;
use Config;
use Storage;
use Log;

class SongRepository{

    /**
     * Find a published song by ID
     *
     * @param  Integer  $id
     * @return Song record
     */
    public function findPublished($id){
        xdebug_break();
        return
            Song::select(DB::raw("
                        songs.id,songs.title,songs.description, songs.duration, TIME_TO_SEC(songs.duration) as seconds,
                        audios.id as audio_id, audios.path as audio_file_path, audios.name as audio_file_name, audios.public as audio_file_public,
                        videos.id as video_id, videos.path as video_file_path, videos.name as video_file_name, videos.public as video_file_public,
                        songs.lyric,songs.total_plays,songs.total_favorites, songs.total_downloads,

                        songs.album_id, albums.title as album_title,albums.release_date,
                        arts.id as art_id, arts.path as art_file_path, arts.name as art_file_name, arts.public as art_file_public,

                        albums.user_id, users.name, users.about, users.image"))
                ->join('albums','albums.id', '=', 'songs.album_id')
                ->join('users','users.id', '=', 'albums.user_id')
                ->leftJoin(DB::raw("(select * from files where files.fileable_type='App\\\\Song') audios"),'audios.fileable_id','=','songs.id')
                ->leftJoin(DB::raw("(select * from files where files.fileable_type='App\\\\Video') videos"),'videos.fileable_id','=','songs.id')
                ->leftJoin(DB::raw("(select * from files where files.fileable_type='App\\\\Album') arts"),'arts.fileable_id','=','albums.id')
                ->where('albums.published',true)
                ->where('songs.id',$id)
                ->first();
    }






    /**
     * Find all published songs by genre
     *
     * @param  Genre  $genre
     * @param  Int    $page
     * @param  String    $popular
     * @return Collection Song records
     */
    public function publishedSongsByGenre($genre, $page = 0, $popular = 'all'){
        if ($popular == 'week') {
            $end = Carbon::now();
            $start = Carbon::now()->subWeek();
            $counts = $this->playedSongsBetween($start, $end);

            $query = $this->publishedByGenre($genre, $page)
                ->join(DB::raw("($counts) counts"), 'counts.song_id', '=', 'songs.id')
                ->select($this->songCommonColumns('counts.plays'))
                ->orderBy('counts.plays', 'DESC');
        } else if ($popular == 'month') {
            $end = Carbon::now();
            $start = Carbon::now()->subMonth();
            $counts = $this->playedSongsBetween($start, $end);
            
            $query = $this->publishedByGenre($genre, $page)
                ->join(DB::raw("($counts) counts"), 'counts.song_id', '=', 'songs.id')
                ->select($this->songCommonColumns('counts.plays'))
                ->orderBy('counts.plays', 'DESC');
        } else {
            $query = $this->publishedByGenre($genre, $page)
                ->select($this->songCommonColumns('songs.total_plays as plays'))
                ->orderBy('songs.total_plays', 'DESC');
        }
        

        return $query->get();
    }
    

    /**
     * Find all published songs by tag
     *
     * @param  String  $tag
     * @param  Int     $page
     * @param  String  $popular
     * @return Collection Song records
     */
    public function publishedSongsByTag($tag, $page = 0, $popular = 'all'){
        if ($popular == 'week') {
            $end = Carbon::now();
            $start = Carbon::now()->subWeek();
            $counts = $this->playedSongsBetween($start, $end);

            $query = $this->publishedByTag($tag, $page)
                ->join(DB::raw("($counts) counts"), 'counts.song_id', '=', 'songs.id')
                ->select($this->songCommonColumns('counts.plays'))
                ->orderBy('counts.plays', 'DESC');
        } else if ($popular == 'month') {
            $end = Carbon::now();
            $start = Carbon::now()->subMonth();
            $counts = $this->playedSongsBetween($start, $end);
            
            $query = $this->publishedByTag($tag, $page)
                ->join(DB::raw("($counts) counts"), 'counts.song_id', '=', 'songs.id')
                ->select($this->songCommonColumns('counts.plays'))
                ->orderBy('counts.plays', 'DESC');
        } else {
            $query = $this->publishedByTag($tag, $page)
                ->select($this->songCommonColumns('songs.total_plays as plays'))
                ->orderBy('songs.total_plays', 'DESC');
        }

        return $query->get();
    }

    /**
     * Count all published songs by genre
     *
     * @param  Genre  $genre
     * @param  Int    $page
     * @return Collection Song records
     */
    public function countPublishedSongsByGenre($genre){
        $query = Song::select('songs.id')
                    ->join('albums','albums.id','=','songs.album_id')
                    ->where('albums.published',true);

        if($genre){
            $query->where('albums.genre_id',$genre->id);
        }

        return $query->count();
    }

    /***********************************************************************
     *  The following private functions are utilities to build
     *  reusable queries.
     ***********************************************************************/

    /**
     * Builds a query to get published songs by tag
     */
    private function publishedByTag($tag, $page) {
        $pageSize = Config::get('app.page_size');

        return Song::join('albums','albums.id','=','songs.album_id')
            ->join('users','users.id','=','albums.user_id')
            ->join('tagged', 'tagged.taggable_id', '=', 'songs.id')
            ->join('tags', 'tags.id', '=', 'tagged.tag_id')
            ->leftJoin(DB::raw("(select * from files where files.fileable_type='App\\\\Song') audios"), 'audios.fileable_id','=','songs.id')
            ->leftJoin(DB::raw("(select * from files where files.fileable_type='App\\\\Album') images"), 'images.fileable_id','=','albums.id')
            ->where('albums.published', true)
            ->where('tagged.taggable_type', 'App\\Song')
            ->where('tags.name', $tag)
            ->skip($pageSize * $page)
            ->take($pageSize);
    }

    /**
     * Builds a query to get published songs by genre
     */
    private function publishedByGenre($genre, $page) {
        $pageSize = Config::get('app.page_size');

        $query = Song::join('albums','albums.id','=','songs.album_id')
            ->join('users','users.id','=','albums.user_id')
            ->leftJoin(DB::raw("(select * from files where files.fileable_type='App\\\\Song') audios"), 'audios.fileable_id','=','songs.id')
            ->leftJoin(DB::raw("(select * from files where files.fileable_type='App\\\\Album') images"), 'images.fileable_id','=','albums.id')
            ->where('albums.published',true)
            ->skip($pageSize * $page)
            ->take($pageSize);

        if ($genre){
            $query->where('albums.genre_id',$genre->id);
        }

        return $query;
    }

    /**
     * Returns the select statemente by receiving a column to set the plays
     * between two dates.
     */
    private function songCommonColumns($playsColumns = 'songs.total_plays as plays') {
        return DB::raw(
            'songs.id, songs.title, songs.total_plays,songs.total_favorites,songs.total_downloads,songs.created_at, songs.duration,'
            .'audios.id as audio_file_id, audios.name as audio_file_name, audios.path as audio_file_path, audios.public as audio_file_public,'
            .'images.id as art_file_id, images.name as art_file_name, images.path as art_file_path, images.public as art_file_public,'
            .'albums.user_id, users.name, users.image as user_image,'
            ."songs.album_id, $playsColumns"
        );
    }

    /**
     * Builds a query to get the songs played between two dates
     */
    private function playedSongsBetween($start, $end) {
        return DB::raw("
            select reference_id as song_id, count(reference_id) as plays
            from activities
            where action='play' and created_at between '$start' and '$end'
            group by reference_id
        ");
    }
}
