<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = ['thread_id', 'user_id'];

    /**
     * Participant belongs to a user.
     *
     * @return User
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * Participant belongs to a thread.
     *
     * @return User
     */
    public function thread()
    {
        return $this->belongsTo('App\Thread');
    }
}
