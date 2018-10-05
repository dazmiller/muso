<?php

namespace App\Http\Serializers;

use Log;
use App\Album;
use App\Post;
use App\Song;
use App\User;

class ActivitySerializer extends BaseSerializer {
  protected $ids = [
    'id',
  ];

  protected $basic = [
    'action',
    ['name' => 'model', 'mapping' => 'reference_type'],
    ['name' => 'time', 'mapping' => 'created_at'],
    ['name' => 'author', 'mapping' => 'user_id'],
  ];

  public function __construct(UserSerializer $userSerializer, AssetSerializer $assetSerializer) {
    $this->assetSerializer = $assetSerializer;
    $this->userSerializer = $userSerializer;
  }

  protected function parseUser_id($activity) {
    return $this->userSerializer->one($activity, ['minimal']);
  }

  protected function parseReference_type($activity) {
    switch ($activity->action) {
      case 'avatar':
        return [
          'url' => $this->assetSerializer->getFileURL((Object)[
            'id'  => $activity->file_id,
            'name'  => $activity->file_name,
            'path'  => $activity->file_path,
            'public'  => $activity->file_public,
          ])
        ];
      case 'follow':
      case 'followed':
      case 'unfollow':
        $user = User::find($activity->reference_id);
        if ($user) {
          return [
            'type' => 'user',
            'id' => $user->id,
            'name' => $user->name,
            'image'=> $user->image,
          ];
        }
        break;
      case 'published-album':
        $album = Album::find($activity->reference_id);
        if ($album) {
          return [
            'id'  => $album->id,
            'title' => $album->title,
            'image' => $this->assetSerializer->getFileURL($album->image)
          ];
        }
        break;
      case 'like' || 'unlike' || 'download' || 'comment':
        if ($activity->reference_type == 'App\\Song') {
          $song = Song::find($activity->reference_id);
          if ($song) {
            return [
              'type' => 'song',
              'id'  => $song->id,
              'title'  => $song->title,
            ];
          }
        }

        if ($activity->reference_type == 'App\\Post') {
          $post = Post::find($activity->reference_id);

          if ($post) {
            return [
              'type' => 'post',
              'id'  => $post->id,
              'title'  => $post->title,
              'image' => $post->asset()->exists() ? $this->assetSerializer->getFileURL($post->asset) : '',
            ];
          }
        }
        break;
      }

    return [];
  }
}
