<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Overtrue\LaravelFollow\Traits\CanFollow;
use Overtrue\LaravelFollow\Traits\CanBeFollowed;
use DB;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;
    use CanFollow;
    use CanBeFollowed;

    /**
     * To set the users per page when using the laravel paginator
     */
    protected $perPage = 25;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'password', 'country', 'about', 'image', 'occupation', 'website', 'gender', 'dob'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'email', 'password',  'remember_token', 'facebook', 'twitter', 'latitude', 'longitude', 'postcode', 'recovery_token', 'recovery_sent_at', 'confirmation_token', 'confirmation_sent_at',
    ];

    protected $casts = [
        'admin' => 'boolean',
        'author' => 'boolean',
    ];

    /**
     * Dates in this model
     */
    protected $dates = ['deleted_at'];

    /**
     * Get the albums for the author
     */
    public function albums()
    {
        return $this->hasMany('App\Album');
    }

    /**
     * Get the posts for this author
     */
    public function posts()
    {
        return $this->hasMany('App\Post','author_id');
    }

    /**
     * Get the images for the user
     */
    public function images()
    {
        return $this->morphMany('App\User','fileable');
    }

    /**
     * Get the activities for the user
     */
    public function activities()
    {
        return $this->hasMany('App\Activity');
    }

    /**
     * Get the playlists for the author
     */
    public function playlists()
    {
        return $this->hasMany('App\Playlist');
    }

    /**
     * Search for users by name and return the first 10
     */
    public function scopeSearch($query, $keyword) {
        return $query->where('name', 'like', "%$keyword%")
                     ->limit(10)
                     ->orderBy('name', 'ASC');
    }

    /**
     * Search for users that are authors
     */
    public function scopeSearchAuthors($query, $keyword) {
        return $query->where('name', 'like', "%$keyword%")
                    ->where('users.author', true)
                    ->orderBy('name', 'ASC');
    }

    /**
     * Scope to return the users who liked the given song
     */
    public function scopeUsersWhoLikedSong($query, $song_id) {
        return $query->select(DB::raw('users.*'))
            ->join('likeable_likes', function($join) {
                $join->on('likeable_likes.user_id', '=', 'users.id');
                $join->on('likeable_likes.likable_type', '=', DB::raw('\'App\\\\Song\''));
            })
            ->where('likeable_likes.likable_id', '=', $song_id)
            ->take(40)
            ->orderBy('likeable_likes.created_at', 'DESC');
    }

    /**
     * Apply filters to the query, currently supports:
     * author = true | false
     * gender = Male | Female | Other
     * name= Any string to search
     * country = Any Country Name
     * occupation = Any Occupation
     */
    public function scopeFilters($query, $filters) {
        if (isset($filters['gender'])) {
            $query = $query->where('users.gender', $filters['gender']);
        }

        if (isset($filters['country'])) {
            $query = $query->where('users.country', $filters['country']);
        }

        if (isset($filters['occupation'])) {
            $query = $query->where('users.occupation', $filters['occupation']);
        }

        if (isset($filters['author'])) {
            $query = $query->where('users.author', $filters['author']);
        }

        if (isset($filters['name'])) {
            $query = $query->where('users.name', 'like', '%'.$filters['name'].'%');
        }

        return $query;
    }

    public function scopeCountries($query, $authors) {
        $query = $query->select(DB::raw('users.country as name, count(users.country) as total'))
            ->whereNotNull('users.country')
            ->groupBy('users.country')
            ->orderBy('total', 'DESC');

        if ($authors) {
            $query = $query->where('users.author', true);
        }

        return $query;
    }

    /**
     * Returns users registered by between the given two dates
     */
    public function scopeRegisteredBetween($query, $from, $to) {
        return $query->whereBetween('users.created_at', [$from, $to]);
    }

    /**
     * Returns most popular authors between two dates
     */
    public function scopePopularBetween($query, $from, $to) {
        return $query->select(DB::raw('users.id, users.name, users.image, sum(totals.total) as plays'))
                ->join(DB::raw("(
                    select albums.user_id, albums.id, sum(stats.total) as total
                    from albums
                    inner join (
                        select songs.album_id, activities.reference_id, count(activities.reference_id) as total
                        from activities
                        inner join songs on songs.id=activities.reference_id
                        where activities.action='play' and activities.reference_type='App\\\\Song' and activities.created_at between '$from' and '$to'
                        group by songs.album_id, activities.reference_id
                    ) stats on albums.id=stats.album_id
                    group by albums.user_id, albums.id, stats.album_id
                ) totals"), 'totals.user_id', '=', 'users.id')
                ->groupBy('users.id', 'users.name', 'users.image')
                ->orderBy('plays', 'desc');
    }
}
