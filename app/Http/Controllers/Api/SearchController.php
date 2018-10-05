<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repositories\AlbumRepository;
use App\Repositories\SongRepository;
use App\Repositories\UserRepository;
use App\Http\Serializers\AlbumSerializer;
use App\Http\Serializers\SongSerializer;
use App\Http\Serializers\UserSerializer;
use App\Album;
use App\Song;
use App\User;
use Config;
use Log;

class SearchController extends Controller
{
    /**
     * The album repository instance.
     *
     * @var AlbumRepository
     */
    protected $songSerializer;

    /**
     * Create a new controller instance.
     *
     * @param  TaskRepository  $tasks
     * @return void
     */
    public function __construct(
      SongSerializer $songSerializer,
      AlbumSerializer $albumSerializer,
      UserSerializer $userSerializer
    )
    {
      $this->songSerializer = $songSerializer;
      $this->albumSerializer = $albumSerializer;
      $this->userSerializer = $userSerializer;
    }

    /**
     * Search for songs by title
     */
    public function songs(Request $request) {
      $songs = Song::search($request->input('query'), true)->get();

      return response()->json([
        'success'   => true,
        'meta' => [
          'total' => Song::search($request->input('query'), true)->count(),
        ],
        'songs'    => $this->songSerializer->list($songs, ['basic']),
      ]);
    }

    /**
     * Search for albums by title
     */
    public function albums(Request $request) {
      $albums = Album::search($request->input('query'), true)->paginate();

      return response()->json([
        'success'   => true,
        'meta' => [
          'total' => $albums->total(),
        ],
        'albums'    => $this->albumSerializer->list($albums->items(), ['basic']),
      ]);
    }

    /**
     * Search for songs by title
     */
    public function artists(Request $request) {
      $artists = User::searchAuthors($request->input('query'))->paginate();

      return response()->json([
        'success'   => true,
        'meta' => [
          'total' => $artists->total(),
        ],
        'artists'    => $this->userSerializer->list($artists->items(), ['basic']),
      ]);
    }
}
