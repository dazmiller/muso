<?php

namespace App\Http\Serializers;

class UserSerializer extends BaseSerializer {
  protected $ids = [
    'id',
  ];

  protected $minimal = [
    ['name' => 'id', 'mapping' => 'user_id'],
    'name',
    'image',
  ];
  
  protected $basic = [
    'name',
    'image',
    'occupation',
    'country',
  ];

  protected $stats = [
    'plays',
  ];

  protected $full = [
    'website',
    'about',
    'gender',
    'author',
    'admin',
  ];
  
  protected $private = [
    'dob',
    'latitude',
    'longitude',
    'postcode',
    'email',
  ];

  protected $followable = [
    ['name' => 'time', 'mapping' => 'pivot'],
  ];

  protected function parsePivot($model) {
    return $model->pivot->created_at;
  }
  
  protected function parseUser_id($model) {
    return $model->user_id;
  }
}