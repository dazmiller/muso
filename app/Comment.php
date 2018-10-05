<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model {
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = ['title', 'body'];

    /**
     * Helper method to check if a comment has children.
     *
     * @return bool
     */
    public function hasChildren()
    {
        // @TODO Implement tree view
        // return $this->children()->count() > 0;
        return false;
    }

    /**
     * @return mixed
     */
    public function commentable()
    {
        return $this->morphTo();
    }

    /**
     * Comment belongs to a user.
     *
     * @return User
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * Returns the published comments by the given dates
     */
    public function scopePublishedBetween($query, $from, $to) {
        return $query->whereBetween('comments.created_at', [$from, $to])
                    ->where('comments.published', true);
    }
}