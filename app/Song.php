<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Conner\Likeable\LikeableTrait;
use Cartalyst\Tags\TaggableTrait;
use Cartalyst\Tags\TaggableInterface;
use DB;

class Song extends Model implements TaggableInterface
{
    use LikeableTrait;
    use TaggableTrait;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'description', 'lyric', 'duration'];

    /**
     * Get the album of this song.
     */
    public function album()
    {
        return $this->belongsTo('App\Album');
    }

    /**
     * The playlists that belong to the song.
     */
    public function playlists()
    {
        return $this->belongsToMany('App\Playlist');
    }

    /**
     * Get the assigned sound file for this song
     */
    public function file()
    {
        return $this->morphOne('App\File', 'fileable');
    }

    /**
     * Relationship to Comment model
     */
    public function comments() {
        return $this->morphMany('App\Comment', 'commentable');
    }

    /**
     * Scope to search songs by title
     */
    public function scopeSearch($query, $title, $published = false) {
        $query = $query->select(DB::raw(DB::raw("
                    songs.id,songs.title,songs.description, songs.duration, TIME_TO_SEC(songs.duration) as seconds, songs.created_at,
                    audios.id as audio_id, audios.path as audio_file_path, audios.name as audio_file_name, audios.public as audio_file_public,
                    songs.lyric,songs.total_plays,songs.total_favorites,songs.total_downloads,

                    songs.album_id, albums.title as album_title,albums.release_date,
                    arts.id as art_id, arts.path as art_file_path, arts.name as art_file_name, arts.public as art_file_public,

                    albums.user_id, users.name, users.about, users.image")))
                ->join('albums','albums.id', '=', 'songs.album_id')
                ->join('users','users.id', '=', 'albums.user_id')
                ->leftJoin(DB::raw("(select * from files where files.fileable_type='App\\\\Song') audios"),'audios.fileable_id','=','songs.id')
                ->leftJoin(DB::raw("(select * from files where files.fileable_type='App\\\\Album') arts"),'arts.fileable_id','=','albums.id')
                ->where('songs.title', 'like', "%$title%");
        
        if ($published) {
            $query = $query->where('albums.published', $published);
        }

        return $query;
    }

    /**
     * Scope to return the songs with most plays between two dates
     */
    public function scopePopularBetween($query, $from, $to) {
        return $query->select(DB::raw("
                    songs.id, songs.title, plays.total_plays,
                    songs.album_id, albums.title as album_title,
                    files.id as art_id, files.path as art_file_path, files.name as art_file_name, files.public as art_file_public,
                    albums.user_id, users.name, users.image
                "))
                ->join(DB::raw("(
                    select activities.reference_id, count(activities.reference_id) as total_plays
                    from activities
                    where activities.action='play' and activities.reference_type='App\\\\Song' and activities.created_at between '$from' and '$to'
                    group by activities.reference_id
                    order by total_plays desc
                    ) plays
                "), 'songs.id', '=', 'plays.reference_id')
                ->join('albums', 'albums.id', '=', 'songs.album_id')
                ->join('users', 'albums.user_id', '=', 'users.id')
                ->join('files',function ($join) {
                    $join->on('files.fileable_id','=','albums.id')
                            ->where('files.fileable_type','=','App\\Album');
                });
    }
}
