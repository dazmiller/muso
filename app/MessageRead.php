<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MessageRead extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = ['user_id', 'message_id'];

    /**
     * Message Read belongs to a user.
     *
     * @return User
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * Message Read belongs to a Message.
     *
     * @return User
     */
    public function message()
    {
        return $this->belongsTo('App\Message');
    }
}
