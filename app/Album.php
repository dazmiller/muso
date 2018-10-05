<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Album extends Model
{
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['genre_id','title', 'description','release_date'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'published' => 'boolean',
    ];

    /**
     * Get the author of this album.
     */
    public function author()
    {
        return $this->belongsTo('App\User','user_id');
    }

    /**
     * Get the assigned image for this album
     */
    public function image()
    {
        return $this->morphOne('App\File', 'fileable');
    }

    /**
     * Get the songs for the album
     */
    public function songs()
    {
        return $this->hasMany('App\Song');
    }

    /**
     * Get the genre of this album.
     */
    public function genre()
    {
        return $this->belongsTo('App\Genre');
    }

    /**
     * Scope to search albums by title
     */
    public function scopeSearch($query, $title, $published = false) {
        $query = $query->select(DB::raw(DB::raw("
                    albums.id, albums.title, albums.published, albums.release_date, albums.created_at,
                    arts.id as art_id, arts.path as art_file_path, arts.name as art_file_name, arts.public as art_file_public,
                    albums.genre_id, genres.name as genre_name,
                    albums.user_id, users.name, users.image")))
                ->join('users','users.id', '=', 'albums.user_id')
                ->join('genres','genres.id', '=', 'albums.genre_id')
                ->leftJoin(DB::raw("(select * from files where files.fileable_type='App\\\\Album') arts"),'arts.fileable_id','=','albums.id')
                ->where('albums.title', 'like', "%$title%");
        
        if ($published) {
            $query = $query->where('albums.published', $published);
        }

        return $query;
    }

    /**
     * Returns the published albums by the given dates
     */
    public function scopePublishedBetween($query, $from, $to) {
        return $query->whereBetween('albums.created_at', [$from, $to])
                    ->where('albums.published', true);
    }

    /**
     * Returns the popular albums between two dates
     */
    public function scopePopularBetween($query, $from, $to) {
        return $query->select(DB::raw('
                    albums.id, albums.title, sum(stats.total) as plays,
                    arts.id as art_id, arts.path as art_file_path, arts.name as art_file_name, arts.public as art_file_public
                '))
                ->join(
                    DB::raw("(
                        select songs.album_id, activities.reference_id, count(activities.reference_id) as total
                        from activities
                        inner join songs on songs.id=activities.reference_id
                        where activities.action='play' and activities.reference_type='App\\\\Song' and activities.created_at between '$from' and '$to'
                        group by songs.album_id, activities.reference_id
                    ) stats"), 'albums.id', '=', 'stats.album_id')
                ->leftJoin(DB::raw("(select * from files where files.fileable_type='App\\\\Album') arts"),'arts.fileable_id','=','albums.id')
                ->groupBy('albums.id', 'albums.title', 'arts.id', 'arts.path', 'arts.name', 'arts.public')
                ->orderBy('plays', 'desc');
    }
}
