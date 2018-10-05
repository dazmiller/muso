<?php

namespace App\Http\Serializers;

class ThreadSerializer extends BaseSerializer {
  protected $ids = [
    'id',
  ];

  /**
   * Returns basic information to display in a list, for example in
   * the inbox page.
   */
  protected $basic = [
    'title',
    'public',
    ['name' => 'excerpt', 'mapping' => 'content'],
    ['name' => 'author', 'mapping' => 'user_id'],
    ['name' => 'time', 'mapping' => 'created_at'],
    ['name' => 'read', 'mapping' => 'is_read'],
  ];

  /**
   * Returns all the information including messages for this thread.
   */
  protected $full = [
    'title',
    'messages',
    'public',
  ];

  public function __construct(MessageSerializer $messageSerializer) {
    $this->messageSerializer = $messageSerializer;
  }

  protected function parseContent($thread) {
    if (is_array($thread)) {
      return substr($thread['content'], 0, 100);
    }

    return substr($thread->content, 0, 100);
  }

  protected function parseUser_id($thread) {
    if (is_array($thread)) {
        return [
        'id'  => $thread['user_id'],
        'name'  => $thread['name'],
        'image'  => $thread['image'],
      ];
    }

    return [
      'id'  => $thread->user_id,
      'name'  => $thread->name,
      'image'  => $thread->image,
    ];
  }
  
  protected function parseMessages($thread) {
    return $this->messageSerializer->list($thread->messages, ['full']);
  }

  protected function parseIs_read($thread) {
    return isset($thread->is_read);
  }
}