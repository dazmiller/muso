<?php

namespace App\Http\Serializers;

class MessageSerializer extends BaseSerializer {
  protected $ids = [
    'id',
  ];

  /**
   * Returns all the information including messages for this thread.
   */
  protected $full = [
    'content',
    ['name' => 'author', 'mapping' => 'user_id'],
    ['name' => 'time', 'mapping' => 'created_at'],
    'read',
  ];

  protected function parseUser_id($message) {
    return [
      'id' => $message->user->id,
      'name' => $message->user->name,
      'image' => $message->user->image,
    ];
  }
}