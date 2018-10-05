<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Conner\Likeable\LikeableTrait;

class Post extends Model
{
    use LikeableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'content','allow_comments'];

    /**
     * Get the author of this post.
     */
    public function author()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * Get the assigned asset file for this post
     */
    public function asset()
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
     * Returns published posts between the given two dates
     */
    public function scopePublishedBetween($query, $from, $to) {
        return $query->whereBetween('posts.created_at', [$from, $to])
                    ->where('posts.published', true);
    }
}
