<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Song;
use JWTAuth;
use Gate;
use Log;

class TagController extends Controller
{

  /**
   * Return all tags for songs
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function song(Request $request)
  {
    $tags = Song::allTags()->orderBy('count', 'DESC')->get();

    return response()->json([
      'success'   => true,
      'meta'      => [
        'total'     => count($tags),
      ],
      'tags'    => $this->prepareData($tags),
    ]);
  }

  private function prepareData($tags) {
    return collect($tags)
      ->map(function ($tag) {
          return [
            'name' => $tag->name,
            'count'=> $tag->count,
          ];
      });
  }
}
