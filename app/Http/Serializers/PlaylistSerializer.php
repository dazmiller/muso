<?php

namespace App\Http\Serializers;

class PlaylistSerializer extends BaseSerializer {
  protected $ids = [
    'id',
  ];

  protected $basic = [
    'title',
    'public',
    ['name' => 'time', 'mapping' => 'created_at'],
  ];
}