<?php

namespace App\Repositories;

use Conner\Likeable\Like;
use App\Activity;
use App\Playlist;
use App\User;
use Config;
use DB;

class PlaylistRepository{

    public function getFavorites(User $user, $page=0){
        $pageSize = Config::get('app.page_size');

        return Like::select(DB::raw('
                      songs.id,songs.title,songs.lyric,songs.duration,TIME_TO_SEC(songs.duration) as seconds,
                      audios.id as audio_id, audios.name as audio_name, audios.path as audio_path, audios.public as audio_public
                    '))
                    ->where('likeable_likes.user_id',$user->id)
                    ->join('songs','songs.id','=','likeable_likes.likable_id')
                    ->join('albums','albums.id','=','songs.album_id')
                    ->leftJoin(DB::raw("(select * from files where files.fileable_type='App\\\\Song') audios"),'audios.fileable_id','=','songs.id')
                    ->where('likeable_likes.likable_type','App\\Song')
                    ->where('albums.published',true)
                    ->orderBy('likeable_likes.created_at','DESC')
                    ->skip($page * $pageSize)
                    ->take($pageSize)
                    ->get();
    }
    
    /**
     * Returns the latests songs played by the given user
     */
    public function getHistory(User $user, $page = 0){
        $pageSize = Config::get('app.page_size');

        return Activity::select(DB::raw('
                      songs.id, songs.title, songs.lyric, songs.duration, TIME_TO_SEC(songs.duration) as seconds, activities.created_at,
                      audios.id as audio_id, audios.name as audio_name, audios.path as audio_path, audios.public as audio_public
                    '))
                    ->join('songs', 'songs.id', '=', 'activities.reference_id')
                    ->join('albums','albums.id','=','songs.album_id')
                    ->leftJoin(DB::raw("(select * from files where files.fileable_type='App\\\\Song') audios"), 'audios.fileable_id', '=', 'songs.id')
                    ->where('activities.reference_type', 'App\\Song')
                    ->where('activities.action', 'play')
                    ->where('activities.user_id', $user->id)
                    ->where('albums.published', true)
                    ->orderBy('activities.created_at', 'DESC')
                    ->skip($page * $pageSize)
                    ->take($pageSize)
                    ->get();
    }

    public function getTotalFavorites(User $user){
        return Like::join('songs','songs.id','=','likeable_likes.likable_id')
                    ->join('albums','albums.id','=','songs.album_id')
                    ->where('likeable_likes.user_id',$user->id)
                    ->where('likeable_likes.likable_type','App\\Song')
                    ->where('albums.published',true)
                    ->count();
    }
    
    /**
     * Returns the total records in the history
     */
    public function getTotalHistory(User $user){
        return Activity::join('songs', 'songs.id', '=', 'activities.reference_id')
                    ->join('albums','albums.id','=','songs.album_id')
                    ->where('activities.action', 'play')
                    ->where('activities.user_id', $user->id)
                    ->where('activities.reference_type', 'App\\Song')
                    ->where('albums.published', true)
                    ->count();
    }

    public function getSongs(Playlist $playlist) {
        return Like::select(DB::raw('
                      songs.id, songs.title, songs.duration, TIME_TO_SEC(songs.duration) as seconds,
                      audios.id as audio_id, audios.name as audio_name, audios.path as audio_path, audios.public as audio_public
                    '))
                    ->from('playlist_song')
                    ->join('songs', 'songs.id', '=', 'playlist_song.song_id')
                    ->join(DB::raw("(select * from files where files.fileable_type='App\\\\Song') audios"), 'songs.id', '=', 'audios.fileable_id')
                    ->join('albums', 'albums.id', '=', 'songs.album_id')
                    ->where('albums.published', true)
                    ->where('playlist_song.playlist_id', $playlist->id)
                    ->orderBy('playlist_song.sorting', 'ASC')
                    ->get();
    }

    /**
     * Deletes all songs from the given playlist. Useful when the user
     * wants to remove the entire playlist.
     */
    public function deleteAllSongsFromPlaylist(Playlist $playlist) {
        return DB::table('playlist_song')
                    ->where('playlist_id', '=', $playlist->id)
                    ->delete();
    }

    /**
     * Deletes a single song from the given playlist. Useful when the user
     * wants to remove a song from one of their playlist
     */
    public function deleteSongFromPlaylist(Playlist $playlist, $songId) {
        return DB::table('playlist_song')
                    ->where('playlist_id', '=', $playlist->id)
                    ->where('song_id', '=', $songId)
                    ->delete();
    }

    /**
     * Deletes a song from ALL playlist from ALL user, this is useful
     * for when the author decides to remove the song or the album
     */
    public function deleteSongFromPlaylists($songId) {
        return DB::table('playlist_song')->where('song_id', '=', $songId)->delete();
    }

    /**
     * Deletes a song from ALL favorites list from ALL user, this is useful
     * for when the author decides to remove the song or the album
     */
    public function deleteSongFromAllFavorites($songId) {
        // Delete from likeable
        DB::table('likeable_likes')
                    ->where('likable_type', '=', 'App\\Song')
                    ->where('likable_id', '=', $songId)->delete();

        // Delete like counters
        DB::table('likeable_like_counters')
                    ->where('likable_type', '=', 'App\\Song')
                    ->where('likable_id', '=', $songId)->delete();
    }
}
