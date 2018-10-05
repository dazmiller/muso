<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'public'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'public' => 'boolean',
    ];
    
    /**
     * The songs that belong to the playlist.
     */
    public function songs()
    {
        return $this->belongsToMany('App\Song')
                    ->withTimestamps();
    }

    /**
     * Get the user of this album.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
