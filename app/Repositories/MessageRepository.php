<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Carbon\Carbon;
use App\Message;
use App\Participant;
use App\Thread;
use App\User;
use DB;
use Log;

class MessageRepository {

  /**
   * Create a new thread message for private conversations
   *
   * @param  User  $album
   * @return Thread $thread, the new thread with participans on it
   */
    public function createThread(User $author, $data){
      DB::beginTransaction();

      try{
        $thread = Thread::create([
          'title' => $data['title'],
        ]);

        // Creating the first message
        $message = new Message([
          'content' => $data['content'],
        ]);
        $message->thread_id = $thread->id;
        $message->user_id = $author->id;
        $message->save();

        // Sender
        Participant::create([
          'thread_id' => $thread->id,
          'user_id' => $author->id,
        ]);

        // Recipients
        $participants = [];
        $now = Carbon::now();
        foreach ($data['recipients'] as $participat) {
          $user = User::find($participat['id']);

          if ($user) {
            array_push($participants, [
              'thread_id' => $thread->id,
              'user_id' => $user->id,
              'created_at' => $now,
              'updated_at' => $now,
            ]);
          } else {
            // If recipient doesn't exist, we don't want to
            // create the thread, only existing user are allowed
            throw new ModelNotFoundException("The user you are trying to send this message doesn't exist.");
          }
        }

        // Insert all participants at once
        Participant::insert($participants);

        DB::commit();
        return $thread;
    } catch(ModelNotFoundException $e) {
      throw $e;
    } catch (\Exception $e){
        DB::rollBack();
        Log::error($e);

        return false;
    }
  }
}