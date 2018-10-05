<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Serializers\AlbumSerializer;
use App\Http\Serializers\SongSerializer;
use App\Http\Serializers\UserSerializer;
use Carbon\Carbon;
use App\Activity;
use App\Album;
use App\Comment;
use App\Post;
use App\Song;
use App\User;
use Validator;
use Gate;
use JWTAuth;
use Log;

class StatisticsController extends Controller
{
  public function __construct(SongSerializer $songSerializer, UserSerializer $userSerializer, AlbumSerializer $albumSerializer) {
    $this->albumSerializer = $albumSerializer;
    $this->songSerializer = $songSerializer;
    $this->userSerializer = $userSerializer;
    $this->validations = [
        'from'    => 'date_format:Y-m-d',
        'to'      => 'date_format:Y-m-d',
        'limit'   => 'numeric',
    ];
  }

  /**
   * Returns the total play, likes, comments and downloads all for songs.
   * If user is admin, it will return the total of all content, if user
   * is an author, it will return totals for his own content.
   */
  public function index(Request $request) {
    $user = JWTAuth::parseToken()->authenticate();

    if (Gate::forUser($user)->allows('statistics', null)) {
      $validator = Validator::make($request->all(), $this->validations);

      if ($validator->fails()) {
        return response()->json([
            'success'=> false,
            'errors' => $validator->errors()->all()
        ], 400);
      }

      $to = Carbon::now();
      $from = Carbon::now()->subYear(); 
      
      if ($request->has('from')) {
        $from = $request->input('from');
      }

      if ($request->has('to')) {
        $to = $request->input('to');
      }

      $data = Activity::statistics($from, $to)->get();
      
      return response()->json([
        'success'   => true,
        'data'     => $data,
      ]);
    }

    return response()->json([
      'success'   => false,
      'errors'    => ['Not authorized to get statistics'],
    ], 403);
  }

  /**
   * Returns data for the overview widgets on the dashboard page
   * @TODO: Receive year as a parameter to retrieve data based on selected year
   */
  public function overview(Request $request) {
    $user = JWTAuth::parseToken()->authenticate();

    if (Gate::forUser($user)->allows('statistics', null)) {
      $now = Carbon::now();
      $beginnigThisMonth = Carbon::createFromDate($now->year, $now->month, 1);
      $beginnigLastMonth = Carbon::createFromDate($now->year, $now->month, 1)->subMonth();
      $beginnigThisYear = Carbon::createFromDate($now->year, 1, 1);

      $usersThisMonth = User::registeredBetween($beginnigThisMonth, $now)->count();
      $usersLastMonth = User::registeredBetween($beginnigLastMonth, $beginnigThisMonth)->count();
      $usersThisYear = User::registeredBetween($beginnigThisYear, $now)->count();

      $articlesThisMonth = Post::publishedBetween($beginnigThisMonth, $now)->count();
      $articlesLastMonth = Post::publishedBetween($beginnigLastMonth, $beginnigThisMonth)->count();
      $articlesThisYear = Post::publishedBetween($beginnigThisYear, $now)->count();

      $albumsThisMonth = Album::publishedBetween($beginnigThisMonth, $now)->count();
      $albumsLastMonth = Album::publishedBetween($beginnigLastMonth, $beginnigThisMonth)->count();
      $albumsThisYear = Album::publishedBetween($beginnigThisYear, $now)->count();

      $commentsThisMonth = Comment::publishedBetween($beginnigThisMonth, $now)->count();
      $commentsLastMonth = Comment::publishedBetween($beginnigLastMonth, $beginnigThisMonth)->count();
      $commentsThisYear = Comment::publishedBetween($beginnigThisYear, $now)->count();

      return response()->json([
        'success'   => true,
        'data'     => [
          'users' => [
            'month' => $usersThisMonth,
            'last' => $usersLastMonth,
            'year' => $usersThisYear,
          ],
          'albums' => [
            'month' => $albumsThisMonth,
            'last' => $albumsLastMonth,
            'year' => $albumsThisYear,
          ],
          'articles' => [
            'month' => $articlesThisMonth,
            'last' => $articlesLastMonth,
            'year' => $articlesThisYear,
          ],
          'comments' => [
            'month' => $commentsThisMonth,
            'last' => $commentsLastMonth,
            'year' => $commentsThisYear,
          ],
        ],
      ]);
    }

    return response()->json([
      'success'   => false,
      'errors'    => ['Not authorized to get statistics'],
    ], 403);
  }

  /**
   * Returns the most played songs by the given dates
   */
  public function songs(Request $request) {
    $user = JWTAuth::parseToken()->authenticate();

    if (Gate::forUser($user)->allows('statistics', null)) {
      $validator = Validator::make($request->all(), $this->validations);

      if ($validator->fails()) {
        return response()->json([
            'success'=> false,
            'errors' => $validator->errors()->all()
        ], 400);
      }

      $to = Carbon::now();
      $from = Carbon::now()->subYear();
      
      if ($request->has('from')) {
        $from = $request->input('from');
      }

      if ($request->has('to')) {
        $to = $request->input('to');
      }

      $songs = Song::popularBetween($from, $to)->paginate($request->input('limit'));

      return response()->json([
        'success'   => true,
        'songs'     => $this->songSerializer->list($songs->items(), ['basic']),
      ]);
    }

    return response()->json([
      'success'   => false,
      'errors'    => ['Not authorized to get statistics'],
    ], 403);
  }

  /**
   * Returns the list of the most popular artists in the giving
   * date range.
   */
  public function artists(Request $request) {
    $user = JWTAuth::parseToken()->authenticate();

    if (Gate::forUser($user)->allows('statistics', null)) {
      $validator = Validator::make($request->all(), $this->validations);

      if ($validator->fails()) {
        return response()->json([
            'success'=> false,
            'errors' => $validator->errors()->all()
        ], 400);
      }

      $to = Carbon::now();
      $from = Carbon::now()->subYear();
      $limit = 10;
      
      if ($request->has('from')) {
        $from = $request->input('from');
      }

      if ($request->has('to')) {
        $to = $request->input('to');
      }
      
      if ($request->has('limit')) {
        $limit = $request->input('limit');
      }

      $artists = User::popularBetween($from, $to)->take($limit)->get();

      return response()->json([
        'success'   => true,
        'artists'     => $this->userSerializer->list($artists, ['basic', 'stats']),
      ]);
    }

    return response()->json([
      'success'   => false,
      'errors'    => ['Not authorized to get statistics'],
    ], 403);
  }

  /**
   * Returns the list of the most popular albums in the giving
   * date range.
   */
  public function albums(Request $request) {
    $user = JWTAuth::parseToken()->authenticate();

    if (Gate::forUser($user)->allows('statistics', null)) {
      $validator = Validator::make($request->all(), $this->validations);

      if ($validator->fails()) {
        return response()->json([
            'success'=> false,
            'errors' => $validator->errors()->all()
        ], 400);
      }

      $to = Carbon::now();
      $from = Carbon::now()->subYear();
      $limit = 10;
      
      if ($request->has('from')) {
        $from = $request->input('from');
      }

      if ($request->has('to')) {
        $to = $request->input('to');
      }

      if ($request->has('limit')) {
        $limit = $request->input('limit');
      }

      $albums = Album::popularBetween($from, $to)->take($limit)->get();

      return response()->json([
        'success'   => true,
        'albums'     => $this->albumSerializer->list($albums, ['basic', 'stats']),
      ]);
    }

    return response()->json([
      'success'   => false,
      'errors'    => ['Not authorized to get statistics'],
    ], 403);
  }
}
