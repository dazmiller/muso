<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = ['content'];

    /**
     * Message belongs to a user.
     *
     * @return User
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * Message belongs to a thread.
     *
     * @return User
     */
    public function thread()
    {
        return $this->belongsTo('App\Thread');
    }
}
