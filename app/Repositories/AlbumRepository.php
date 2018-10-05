<?php

namespace App\Repositories;

use App\Repositories\PlaylistRepository;
use App\User;
use App\Album;
use App\Song;
use DB;
use Storage;
use Log;

class AlbumRepository
{
    public function __construct(PlaylistRepository $playlist)
    {
        $this->playlistRepository = $playlist;
    }

    /**
     * Find one single album by id, it also returns the image from the files table
     *
     * @param  User  $user
     * @return Collection
     */
    public function find($id)
    {
        return Album::select(DB::raw(
                        'albums.id, albums.genre_id, albums.title, albums.description, albums.duration, albums.published, albums.user_id,
                         files.id as file_id, files.name as file_name, files.path, files.public'
                    ))
                    ->where('albums.id',$id)
                    ->leftJoin('files',function ($join) {
                        $join->on('files.fileable_id','=','albums.id')
                             ->where('files.fileable_type','=','App\\Album');
                    })
                    ->first();
    }

    /**
     * Get all of the albums for a given user.
     *
     * @param  User  $user
     * @return Collection
     */
    public function latestsByUser(User $user, $published)
    {
        $query = DB::table('albums')
                    ->select(DB::raw(
                         'albums.id, albums.genre_id, albums.title, albums.description, albums.duration, albums.published, albums.release_date,
                          files.id as file_id, files.name as file_name, files.path, files.public
                        '))
                    ->where('albums.user_id',$user->id)
                    ->leftJoin('files',function ($join) {
                        $join->on('files.fileable_id','=','albums.id')
                             ->where('files.fileable_type','=','App\\Album');
                    })
                    ->orderBy('albums.created_at','DESC');

        if($published){
            $query->where('albums.published',$published == 'true');
        }

        return $query->get();
    }

    /**
     * Get all of the albums from all users, this method is used for the admin only
     *
     * @return Collection
     */
    public function latests($page=0, $published, $search)
    {
        $pageSize = 25;
        $query = DB::table('albums')
                    ->select(DB::raw(
                        'albums.id, albums.genre_id, albums.title, albums.description, albums.duration, albums.published, albums.release_date,
                         files.id as file_id, files.name as file_name, files.path, files.public,
                         albums.user_id, users.name, users.image'
                        ))
                    ->join('users','users.id','=','albums.user_id')
                    ->leftJoin('files',function ($join) {
                        $join->on('files.fileable_id','=','albums.id')
                             ->where('files.fileable_type','=','App\\Album');
                    })
                    ->orderBy('albums.created_at','DESC')
                    ->skip($page * $pageSize)
                    ->take($pageSize);

        if($search){
            $query->where('albums.title','like','%'.$search.'%');
        }

        if($published){
            $query->where('albums.published',$published == 'true');
        }

        return $query->get();
    }

    /**
     * Count all of the albums from all users, this method is used for the admin only
     *
     * @return Collection
     */
    public function latestsCount($search, $published)
    {
        $query = DB::table('albums');

        if($search){
            $query->where('title','like','%'.$search.'%');
        }

        if($published){
            $query->where('published',$published == 'true');
        }

        return $query->count();
    }

    /**
     * Get all the songs from an album with their sound file from the Files table
     *
     * @param  Album  $album
     * @return Collection
     */
    public function findSongs(Album $album)
    {
        return Song::select(DB::raw('
                     songs.id, songs.title, songs.description, songs.lyric, songs.duration, songs.total_favorites, songs.total_plays,
                     files.id as file_id, files.name, files.path, files.original_name, TIME_TO_SEC(songs.duration) as seconds, files.public
                    '))
                    ->where('songs.album_id',$album->id)
                    ->leftJoin('files',function ($join) {
                        $join->on('files.fileable_id','=','songs.id')
                             ->where('files.fileable_type','=','App\\Song');
                    })
                    ->get();
    }

    /**
     * Get a song by user, album and song ID
     *
     * @param  User  $user
     * @param  Integer  $albumId
     * @param  Integer  $songId
     * @return Song
     */
    public function findSongByUser(User $user, $albumId, $songId)
    {
        return Song::select(DB::raw('songs.id,songs.title,songs.description,songs.lyric,songs.duration,files.id as file_id, files.name, files.path, files.original_name'))
                    ->join('albums','albums.id','=','songs.album_id')
                    ->where('songs.album_id',$albumId)
                    ->where('albums.user_id',$user->id)
                    ->where('songs.id',$songId)
                    ->leftJoin('files',function ($join) {
                        $join->on('files.fileable_id','=','songs.id')
                             ->where('files.fileable_type','=','App\\Song');
                    })
                    ->first();
    }

    /**
     * Get a song that belongs to a given album
     *
     * @param  Integer  $albumId
     * @param  Integer  $songId
     * @return Song
     */
    public function findSongInAlbum($albumId, $songId)
    {
        return Song::select(DB::raw('songs.id,songs.title,songs.description,songs.lyric,songs.duration,files.id as file_id, files.name, files.path, files.original_name'))
                    ->join('albums','albums.id','=','songs.album_id')
                    ->where('songs.album_id',$albumId)
                    ->where('songs.id',$songId)
                    ->leftJoin('files',function ($join) {
                        $join->on('files.fileable_id','=','songs.id')
                             ->where('files.fileable_type','=','App\\Song');
                    })
                    ->first();
    }

    /**
     * Delete albumn and all related content, such as songs and image
     *
     * @param  Album  $album
     * @return Boolean true if all records removed, false if not
     */
    public function deleteAlbum(Album $album)
    {
        DB::beginTransaction();

        try{
            //1.- Delete the image
            $image = $album->image;
            if($image){
                $image->delete();
            }

            //2.- Delete song and song file
            $songs = $album->songs;
            foreach ($songs as $song) {
                // Remove tags
                $song->untag();
                
                // Delete song from any playlist
                $this->playlistRepository->deleteSongFromPlaylists($song->id);
                // Delete song from favorites
                $this->playlistRepository->deleteSongFromAllFavorites($song->id);

                $file = $song->file;
                if($file){
                    $file->delete();
                }
                $song->delete();
            }

            //3.- Delete album
            $album->delete();

            DB::commit();
            return true;
        }catch (\Exception $e){
            DB::rollBack();
            Log::error($e);
            return false;
        }
    }

    /**
     * Get latest songs in all publised albums
     *
     * @return Collection Song
     */
    public function latestsPublishedSongs($limit){
        return Song::select(DB::raw(
                    "songs.id, songs.album_id, albums.user_id, songs.title, songs.total_plays, songs.total_favorites, songs.created_at, users.name, songs.duration,
                     audios.id as audio_file_id, audios.path as audio_file_path, audios.name as audio_file_name, audios.public as audio_file_public,
                     arts.id as art_file_id, arts.path as art_file_path, arts.name as art_file_name, arts.public
                    "))
                    ->join('albums','albums.id','=','songs.album_id')
                    ->join('users','users.id','=','albums.user_id')
                    ->leftJoin(DB::raw("(select * from files where files.fileable_type='App\\\\Song') audios"),'audios.fileable_id','=','songs.id')
                    ->leftJoin(DB::raw("(select * from files where files.fileable_type='App\\\\Album') arts"),'arts.fileable_id','=','albums.id')
                    ->where('albums.published',true)
                    ->orderBy('songs.created_at','DESC')
                    ->take($limit)
                    ->get();
    }

    /**
     * Get random songs in all publised albums
     *
     * @return Collection Song
     */
    public function randomPublishedSongs($limit){
        return Song::select(DB::raw(
                    "songs.id, songs.album_id, albums.user_id, songs.title, songs.total_plays, songs.total_favorites, songs.created_at, users.name, songs.duration,
                     audios.id as audio_file_id, audios.path as audio_file_path, audios.name as audio_file_name, audios.public as audio_file_public,
                     arts.id as art_file_id, arts.path as art_file_path, arts.name as art_file_name, arts.public
                    "))
                    ->join('albums','albums.id','=','songs.album_id')
                    ->join('users','users.id','=','albums.user_id')
                    ->leftJoin(DB::raw("(select * from files where files.fileable_type='App\\\\Song') audios"),'audios.fileable_id','=','songs.id')
                    ->leftJoin(DB::raw("(select * from files where files.fileable_type='App\\\\Album') arts"),'arts.fileable_id','=','albums.id')
                    ->where('albums.published',true)
                    ->orderBy(DB::raw('RAND()'))
                    ->take($limit)
                    ->get();
    }

    /**
     * Get the top ten songs in all publised albums.
     *
     * @return Collection Song
     */
    public function topTenPublishedSongs(){
        return Song::select(DB::raw("songs.id, songs.album_id, albums.user_id, songs.duration, songs.title,songs.total_plays, songs.total_favorites, songs.created_at, users.name, users.image as user_image,'' as art_file_id, '' as audio_file_id"))
                    ->join('albums','albums.id','=','songs.album_id')
                    ->join('users','users.id','=','albums.user_id')
                    ->where('albums.published',true)
                    ->orderBy('songs.total_plays','DESC')
                    ->take(10)
                    ->get();
    }

    /**
     * Get the top five artists in all publised albums.
     *
     * @return Collection Song
     */
    public function topArtists($total = 5)
    {
        return DB::select(DB::raw("SELECT users.id, users.name, users.image, sum(songs.total_plays) as plays, sum(songs.total_favorites) as favorites
                                    FROM songs
                                    INNER JOIN albums on albums.id=songs.album_id
                                    INNER JOIN users on users.id=albums.user_id
                                    WHERE albums.published=true
                                    GROUP BY users.id, users.name, users.image
                                    ORDER BY plays DESC
                                    LIMIT :total"),[
                                        'total' => $total
                                    ]);
    }

    /**
     * Get all published albums by user
     */
    public function getPublishedAlbumsByUser(User $user) {
        return Album::select(DB::raw(
                        'albums.id, albums.genre_id, albums.title, albums.description, albums.duration, albums.published, albums.user_id,
                         images.id as file_id, images.name as file_name, images.path, images.public'
                    ))
                    ->where('albums.user_id', $user->id)
                    ->where('albums.published', true)
                    ->leftJoin(DB::raw("(select * from files where files.fileable_type='App\\\\Album') images"), 'images.fileable_id', '=', 'albums.id')
                    ->orderBy('albums.created_at', 'DESC')
                    ->get();
    }
}
