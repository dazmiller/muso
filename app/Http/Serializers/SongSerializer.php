<?php

namespace App\Http\Serializers;

class SongSerializer extends BaseSerializer {
  protected $ids = [
    'id',
  ];

  protected $basic = [
    'title',
    'duration',
    ['name'=>'plays', 'mapping'=>'total_plays'],
    ['name'=>'favorites', 'mapping'=>'total_favorites'],
    ['name'=>'downloads', 'mapping'=>'total_downloads'],
    ['name' => 'album', 'mapping' => 'album_id'],
    ['name' => 'author', 'mapping' => 'user_id'],
    ['name' => 'sound', 'mapping' => 'audio_id'],
    ['name' => 'comments', 'mapping' => 'comments'],
    'tags',
    ['name' => 'time', 'mapping' => 'created_at'],
  ];

  protected $full = [
    'description',
    'lyric',
  ];

  public function __construct(AssetSerializer $assetSerializer, TagSerializer $tagSerializer) {
    $this->assetSerializer = $assetSerializer;
    $this->tagSerializer = $tagSerializer;
  }

  /**
   * Creates the album object
   */
  protected function parseAlbum_id($record) {
    return [
      'id'  => $record->album_id,
      'title' => $record->album_title,
      'image' => $this->assetSerializer->getFileURL((object) [
        'id' => $record->art_id,
        'name'  => $record->art_file_name,
        'path'  => $record->art_file_path,
        'public'  => $record->art_file_public,
      ]),
    ];
  }
  
  /**
   * Creates the sound URL
   */
  protected function parseAudio_id($record) {
    if ($record->audio_id) {
      return $this->assetSerializer->getFileURL((object) [
        'id' => $record->audio_id,
        'name'  => $record->audio_file_name,
        'path'  => $record->audio_file_path,
        'public'  => $record->audio_file_public,
      ]);
    }
  }
  
  /**
   * Creates the author object
   */
  protected function parseUser_id($record) {
    if ($record->user_id) {
      return [
        'id'  => $record->user_id,
        'name' => $record->name,
        'image' => $record->image,
      ];
    }
  }

  /**
   * Creates the tags array
   */
  protected function parseTags($record) {
    return $this->tagSerializer->list($record->tags);
  }
  
  /**
   * Creates the author object
   */
  protected function parseComments($record) {
    return $record->comments->count();
  }
}