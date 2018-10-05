<?php

namespace App\Http\Helpers;

use Illuminate\Support\Facades\Log;

class CommentsHelper {

  /**
   * Creates a tree of comments with only two levels, we don't
   * support more levels of replyes to avoid going too deep.
   */
  public static function createTree($comments) {
    $parents = [];
    $messages = [];

    foreach ($comments as $message) {
      if ($message->published) {
        $comment = [
          'id'        => $message->id,
          'title'     => $message->title,
          'body'      => $message->body,
          'time'      => $message->created_at,
          'author'    => [
            'id'    => $message->user->id,
            'name'  => $message->user->name,
            'image' => $message->user->image,
          ],
          'children' => [],
        ];

        // If is a children, append to the parent
        if($message->parent_id && isset($parents[$message->parent_id])){
          $parents[$message->parent_id]['children'][] = $comment;
        } else {
          // Is a parent, put it as a root
          $parents[$message->id] = $comment;
          $messages[] = &$parents[$message->id];
        }
      }
    }

    return $messages;
  }
}
