<?php

namespace App\Http\Serializers;

class AlbumSerializer extends BaseSerializer {
  protected $ids = [
    'id',
  ];

  protected $basic = [
    'title',
    ['name'=>'genre', 'mapping'=>'genre_id'],
    ['name'=>'author', 'mapping'=>'user_id'],
    ['name'=>'image', 'mapping'=>'art_id'],
    ['name'=>'released', 'mapping'=>'release_date'],
    'published',
    ['name'=>'time', 'mapping'=>'created_at'],
  ];

  protected $stats = [
    'plays',
  ];

  public function __construct(AssetSerializer $assetSerializer) {
    $this->assetSerializer = $assetSerializer;
  }
  
  /**
   * Creates the art URL
   */
  protected function parseArt_id($record) {
    if ($record->art_id) {
      return $this->assetSerializer->getFileURL((object) [
        'id' => $record->art_id,
        'name'  => $record->art_file_name,
        'path'  => $record->art_file_path,
        'public'  => $record->art_file_public,
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
   * Creates the author object
   */
  protected function parseGenre_id($record) {
    if ($record->genre_id) {
      return [
        'id'  => $record->genre_id,
        'name' => $record->genre_name,
      ];
    }
  }
}
