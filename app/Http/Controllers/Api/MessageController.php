<?php

namespace App\Http\Controllers\Api;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Serializers\MessageSerializer;
use App\Http\Serializers\ThreadSerializer;
use App\Http\Serializers\UserSerializer;
use Carbon\Carbon;
use App\Repositories\MessageRepository;
use App\Message;
use App\Participant;
use App\Thread;
use App\MessageRead;
use App\User;
use JWTAuth;
use Gate;
use Validator;
use Config;
use Log;

class MessageController extends Controller {

  public function __construct(
    MessageRepository $messageRepository,
    ThreadSerializer $threadSerializer,
    UserSerializer $userSerializer,
    MessageSerializer $messageSerializer) {
    // Repositories
    $this->messageRepository = $messageRepository;

    // Serializers
    $this->threadSerializer = $threadSerializer;
    $this->messageSerializer = $messageSerializer;
    $this->userSerializer = $userSerializer;

    $this->validations = [
      'title'         => 'required|max:255',
      'content'         => 'required',
      'recipients' => 'required',
      'recipients.*.id' => 'required',
    ];
  }

  /**
   * Show all of the received message threads to the user.
   * Users can only see their own messages, admin doesn't have access to
   * all other messages, only his own.
   * 
   * @return json
   */
  public function received() {
    $user = JWTAuth::parseToken()->authenticate();

    $threads = Thread::received($user->id)->get();

    return response()->json([
      'success'   => true,
      'meta'      => [
        'total'     => 0, // $total,
      ],
      'threads'   => $this->threadSerializer->list($threads, ['basic']),
    ]);
  }

  /**
   * Show all of the sent message threads to the user.
   * 
   * @return json
   */
  public function sent() {
    $user = JWTAuth::parseToken()->authenticate();
      
    // All threads that user is participating in
    $threads = Thread::sent($user->id)->paginate();
    
    return response()->json([
      'success'   => true,
      'meta'      => [
        'total'     => $threads->total(),
      ],
      'threads'   => $this->threadSerializer->list($threads->items(), ['basic']),
    ]);
  }

  /**
   * Show all of the unread message threads to the user.
   * Users can only see their own messages, admin doesn't have access to
   * all other messages, only his own.
   * 
   * @return json
   */
  public function unread(Request $request) {
    $mainmenu = $request->input('mainmenu');
    $user = JWTAuth::parseToken()->authenticate();

    $threads = Thread::unread($user->id)->paginate($request->input('perPage'));
    $total = $threads->total();
    $unreads = $threads->items();
    
    // For the main menu we always display only 5 items
    // at the time. If less than 5 unreads, show the last
    // received messages.
    $numberOfMessages = 5;
    if ($mainmenu && count($threads->items()) < $numberOfMessages) {
      $missing = $numberOfMessages - count($threads->items());
      $received = Thread::received($user->id)->limit($missing)->get();
      $unreads = array_merge($unreads, $received->toArray());
      $unreads = collect($unreads)->unique('id')->values()->all();
    }

    return response()->json([
      'success'   => true,
      'meta'      => [
        'total' => $total,
      ],
      // 'threads' => $unreads
      'threads'   => $this->threadSerializer->list($unreads, ['basic']),
    ]);
  }

  /**
   * Shows a message thread.
   *
   * @param $id
   * @return mixed
   */
  public function show($id) {
    $thread = Thread::find($id);
    
    if ($thread) {
      $user = JWTAuth::parseToken()->authenticate();
      $participant = Participant::where([
        'thread_id' => $thread->id,
        'user_id'   => $user->id,
      ])->first();

      // Check if the current user is a participant in this thread
      if ($participant) {
        foreach ($thread->messages as $message) {
          try {
            MessageRead::create([
              'user_id' => $user->id,
              'message_id' => $message->id,
            ]);
            $message->read = false;
          } catch(QueryException $e){
            // If there's an erro while saving the read status
            // it means the user already read this message.
            $message->read = true;
          }
        }

        return response()->json([
          'success'   => true,
          'meta'      => [],
          'thread'    => $this->threadSerializer->one($thread, ['full']),
        ]); 
      }

      return response()->json([
        'success'   => false,
        'errors'    => ['You are not part of this conversation'],
      ], 403);
    }
    
    return response()->json([
      'success'=> false,
      'errors' => ['Conversation not found']
    ], 404);
  }

  /**
   * Stores a new message thread.
   *
   * @return mixed
   */
  public function store(Request $request){
    $user = JWTAuth::parseToken()->authenticate();
    $validator = Validator::make($request->all(), $this->validations);

    if ($validator->fails()) {
      return response()->json([
        'success'=> false,
        'errors' => $validator->errors()->all()
      ], 400);
    }

    try {
      $thread = $this->messageRepository->createThread($user, $request->all());

      if ($thread) {
        return response()->json([
          'success'   => true,
          'message'  => 'Message successfully sent.',
          'thread'   => $this->threadSerializer->one($thread),
        ]);
      }
    } catch (ModelNotFoundException $e) {
      return response()->json([
        'success'   => false,
        'errors'    => [$e->getMessage()],
      ], 400);
    }

    return response()->json([
      'success'   => false,
      'errors'    => ['There was an error while sending this message, please try again.'],
    ], 401);
  }

  /**
   * Adds a new message to a current thread. It's not possible to add
   * more participants to the thread and only the current participants
   * can add new messages to the given thread.
   *
   * @param $id The thread to add the message to
   * @return json
   */
  public function update(Request $request, $id) {
    $user = JWTAuth::parseToken()->authenticate();


    $validator = Validator::make($request->all(), [
      'content'         => 'required',
    ]);

    if ($validator->fails()) {
      return response()->json([
        'success'=> false,
        'errors' => $validator->errors()->all(),
      ], 400);
    }

    $thread = Thread::find($id);
    if ($thread) {
      $participant = Participant::where([
        'thread_id' => $thread->id,
        'user_id'   => $user->id,
      ])->first();

      if ($participant) {
        // Message
        $message = new Message([
          'content' => $request->input('content'),
        ]);
        $message->thread_id = $thread->id;
        $message->user_id = $user->id;
        $message->save();

        return response()->json([
          'success'   => true,
          'message'   => 'Message sent',
          'mail' => $this->messageSerializer->one($message),
        ]);
      }

      return response()->json([
        'success'   => false,
        'errors'    => ['You are not part of this conversation'],
      ], 403);
    }

    return response()->json([
      'success'=> false,
      'errors' => ['Conversation not found']
    ], 404);
  }

  /**
   * Search for users to send messages, it only returns the top 10
   * results with basic information only.
   */
  public function users(Request $request) {
    $keyword = $request->input('search');

    if ($keyword) {
      $users = User::search($keyword)->get();
    } else {
      $users = [];
    }

    return response()->json([
      'success'=> true,
      'users' => $this->userSerializer->list($users, ['basic']),
    ]);
  }
}
