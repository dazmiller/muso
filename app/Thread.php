<?php

namespace App;

use \Illuminate\Database\Eloquent\ModelNotFoundException;
use \Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use DB;

class Thread extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = ['title'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'public' => 'boolean',
    ];

    /**
     * Thread belongs to a user.
     *
     * @return User
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * Get the messages for the thred
     */
    public function messages()
    {
        return $this->hasMany('App\Message');
    }

    /**
     * Get the participants for the thread
     */
    public function participants()
    {
        return $this->hasMany('App\Participant');
    }

    /**
     * Get the latest received messages for the giving user
     */
    public function scopeReceived(Builder $query, $user_id) {
        // $user_id should always be an integer, this validation will prevent
        // a sql injection on the raw query.
        if (FALSE === is_int($user_id)) {
            throw new ModelNotFoundException();
        }

        $latest_message = DB::raw("
                (SELECT max(messages.id) as id, messages.thread_id
                from messages
                INNER JOIN threads  on threads.id=messages.thread_id
                INNER JOIN participants on participants.thread_id=threads.id
                WHERE participants.user_id=$user_id and messages.user_id!=$user_id
                GROUP BY messages.thread_id) last_message
            ");

        return $query->select(DB::raw('
                threads.id, threads.title, threads.public,
                messages.content, messages.user_id, messages.created_at,
                users.name, users.image,
                message_reads.created_at as is_read
            '))
            ->join('messages', 'threads.id', '=', 'messages.thread_id')
            ->join('users', 'users.id', '=', 'messages.user_id')
            ->join($latest_message, 'last_message.id', '=', 'messages.id')
            ->leftJoin('message_reads', function($join) use ($user_id) {
                $join->on('message_reads.message_id', '=', 'messages.id');
                $join->on('message_reads.user_id', '=', DB::raw($user_id));
            })
            ->orderBy('messages.created_at', 'DESC');
    }

    /**
     * Get the latest unread messages for the giving user
     */
    public function scopeUnread(Builder $query, $user_id) {
        // $user_id should always be an integer, this validation will prevent
        // a sql injection on the raw query.
        if (FALSE === is_int($user_id)) {
            throw new ModelNotFoundException();
        }

        $latest_message = DB::raw("
                (SELECT max(messages.id) as id, messages.thread_id
                from messages
                INNER JOIN threads  on threads.id=messages.thread_id
                INNER JOIN participants on participants.thread_id=threads.id
                WHERE participants.user_id=$user_id and messages.user_id!=$user_id
                GROUP BY messages.thread_id) last_message
            ");

        return $query->select(DB::raw('
                threads.id, threads.title, threads.public,
                messages.content, messages.user_id, messages.created_at,
                users.name, users.image,
                message_reads.created_at as is_read
            '))
            ->join('messages', 'threads.id', '=', 'messages.thread_id')
            ->join('users', 'users.id', '=', 'messages.user_id')
            ->join($latest_message, 'last_message.id', '=', 'messages.id')
            ->leftJoin('message_reads', function($join) use ($user_id) {
                $join->on('message_reads.message_id', '=', 'messages.id');
                $join->on('message_reads.user_id', '=', DB::raw($user_id));
            })
            ->whereNull('message_reads.user_id')
            ->orderBy('messages.created_at', 'DESC');
    }

    /**
     * Returns the messages sent by the given user
     */
    public function scopeSent(Builder $query, $user_id) {
        // $user_id should always be an integer, this validation will prevent
        // a sql injection on the raw query.
        if (FALSE === is_int($user_id)) {
            throw new ModelNotFoundException();
        }

        $latest_threads = DB::raw("
            (select messages.thread_id as id, threads.title, max(messages.id) as message_id, threads.public
            from threads 
            inner join messages on threads.id=messages.thread_id
            inner join participants on threads.id=participants.thread_id
            where participants.user_id=$user_id and messages.user_id=$user_id
            group by messages.thread_id, threads.title, threads.public) last_treads
        ");

        return $query->select(DB::raw('
                last_treads.id, last_treads.title, last_treads.public,
                messages.content, messages.created_at,
                users.id as user_id, users.name, users.image,
                1 as is_read
            '))
            ->from('participants')
            ->join($latest_threads, 'last_treads.id', '=', 'participants.thread_id')
            ->join('messages', 'messages.id', '=', 'last_treads.message_id')
            ->join('users', 'users.id', '=', 'participants.user_id')
            ->where('participants.user_id', '!=', $user_id)
            ->orderBy('messages.created_at', 'DESC');
    }
}
