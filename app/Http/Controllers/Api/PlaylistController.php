<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Database\QueryException;
use App\Http\Controllers\Controller;
use App\Http\Serializers\PlaylistSerializer;
use App\Repositories\PlaylistRepository;
use App\Repositories\SongRepository;
use App\Playlist;
use App\Activity;
use App\Song;
use JWTAuth;
use Validator;
use Gate;
use Log;

/**
 * Class PlaylistController
 *
 * @package App\Http\Controllers\v1
 */
class PlaylistController extends Controller{
    /**
     * The playlist repository instance.
     *
     * @var PlaylistRepository
     */
    protected $playlistRepository;

    /**
     * The song repository instance.
     *
     * @var SongRepository
     */
    protected $songRepository;

    /**
     * Create a new controller instance.
     *
     * @param  PlaylistRepository  $playlistRepository
     * @return void
     */
    public function __construct(PlaylistRepository $playlist, SongRepository $song, PlaylistSerializer $playlistSerializer){
        $this->playlistRepository = $playlist;
        $this->songRepository = $song;
        $this->playlistSerializer = $playlistSerializer;

        $this->validations = [
            'title'         => 'required|min:3|max:255'
        ];
    }

    /**
     * Display a listing of playlists for the current user
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = JWTAuth::parseToken()->authenticate();

        return response()->json([
            'success' => true,
            'playlists' => $this->playlistSerializer->list($user->playlists, ['basic'])
        ]);
    }

     /**
      * Store a newly created playlist in storage.
      *
      * @return \Illuminate\Http\JsonResponse
      *
      * @SWG\Post(
      *     path="/api/v1/playlists",
      *     description="Creates a new playlist for the current user.",
      *     operationId="api.playlist.store",
      *     produces={"application/json"},
      *     tags={"playlist"},
      *
      *     @SWG\Parameter(
      *         description="The token authentication",
      *         in="header",
      *         name="Authorization",
      *         required=true,
      *         type="string",
      *         format="string"
      *     ),
      *     @SWG\Parameter(
      *         description="The playlist model",
      *         in="body",
      *         name="body",
      *         required=true,
      *         @SWG\Schema(ref="#/definitions/Playlist"),
      *     ),
      *
      *     @SWG\Response(
      *         response=200,
      *         description="Successfully saved."
      *     ),
      *     @SWG\Response(
      *         response=400,
      *         description="Invalid token."
      *     )
      * )
      */
    public function store(Request $request){
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), $this->validations);

        if ($validator->fails()) {
            return response()->json([
                'success'=> false,
                'errors' => $validator->errors()->all()
            ],400);
        }

        $playlist = $user->playlists()->create($request->all());

        if($playlist->public){
            $activity = new Activity();
            $activity->fill([
                'action'            => 'playlist',
                'user_id'           => $user->id,
                'reference_type'    => 'App\\Playlist',
                'reference_id'      => $playlist->id
            ]);
            $activity->save();
        }

        return response()->json([
            'success'   => true,
            'playlist'  => $playlist
        ]);
    }

    /**
     * Add a song to a playlist.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request, $id){
        $user       = JWTAuth::parseToken()->authenticate();
        $playlist   = Playlist::find($id);

        if (Gate::forUser($user)->allows('playlist-add-song', $playlist)) {

            $song = $this->songRepository->findPublished($request->song);
            if($song){

                try{
                    $playlist->songs()->save($song,['sorting'=>$playlist->songs()->count()]);

                    if($playlist->public){
                        $activity = new Activity();
                        $activity->fill([
                            'action'            => 'playlist-add-song',
                            'user_id'           => $user->id,
                            'reference_type'    => 'App\\Playlist',
                            'reference_id'      => $playlist->id
                        ]);
                        $activity->save();
                    }

                    return response()->json([
                        'success'   => true,
                        'message'   => 'Song successfully added to your playlist.'
                    ]);
                }catch(QueryException $e){
                    return response()->json([
                        'success'   => false,
                        'errors'    => ['Song already exist on this playlist'],
                    ],400);
                }
            }
        }

        return response()->json([
            'success'   => false,
            'errors'    => ['Not authorized to add this song to this playlist'],
        ],403);
    }

    /**
     * Removes a single song from the given playlist.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function remove(Request $request, $playlist_id, $id){
        $user       = JWTAuth::parseToken()->authenticate();
        $playlist   = Playlist::find($playlist_id);

        if (Gate::forUser($user)->allows('playlist-remove-song', $playlist)) {
            
            // Removing the song from playlist
            $success = $this->playlistRepository->deleteSongFromPlaylist($playlist, $id);

            if($success){
                return response()->json([
                    'success'   => true,
                    'message'   => 'Song removed from your playlist.'
                ]);
            }

            return response()->json([
                'success'   => false,
                'message'   => 'The song does not exist in the playlist.'
            ]);
        }

        return response()->json([
            'success'   => false,
            'errors'    => ['Not authorized to remove the song from this playlist'],
        ], 403);
    }

    /**
     * Returns the playlist with songs
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/api/v1/playlists/{id}",
     *     description="Returns the specified playlist with all their songs.",
     *     operationId="api.playlist.show",
     *     produces={"application/json"},
     *     tags={"playlist"},
     *
     *     @SWG\Parameter(
     *         description="The token authentication",
     *         in="header",
     *         name="Authorization",
     *         required=true,
     *         type="string",
     *         format="string"
     *     ),
     *     @SWG\Parameter(
     *         description="The playlist ID",
     *         in="path",
     *         name="id",
     *         required=true,
     *         type="integer",
     *         format="int32"
     *     ),
     *
     *     @SWG\Response(
     *         response=200,
     *         description="The playlist with songs."
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="Playlist not found."
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Invalid token."
     *     )
     * )
     */
    public function show($id){
        $user = JWTAuth::parseToken()->authenticate();
        $playlist = Playlist::find($id);

        if ($playlist) {
            if (Gate::forUser($user)->allows('playlist-show', $playlist)) {
                $songs = $this->playlistRepository->getSongs($playlist);

                return response()->json([
                    'success'   => true,
                    'playlist'  => $this->preparePlaylist($playlist, $songs)
                ]);
            }

            return response()->json([
                'success'   => false,
                'message'    => ['Access denied, you do not have access to read this playlist'],
            ], 403);
        }

        return response()->json([
            'success'   => false,
            'message'    => 'Playlist not found',
        ],404);
    }

     /**
      * Returns the favorites songs
      *
      * @return \Illuminate\Http\JsonResponse
      *
      * @SWG\Get(
      *     path="/api/v1/playlists/favorites",
      *     description="Returns the list of favorites songs for the current user",
      *     operationId="api.playlist.favorites",
      *     produces={"application/json"},
      *     tags={"playlist"},
      *
      *     @SWG\Parameter(
      *         description="The token authentication",
      *         in="header",
      *         name="Authorization",
      *         required=true,
      *         type="string",
      *         format="string"
      *     ),
      *     @SWG\Parameter(
      *         description="The page to retrieve, defaults to 0",
      *         in="query",
      *         name="page",
      *         required=false,
      *         type="integer",
      *         format="int32"
      *     ),
      *
      *     @SWG\Response(
      *         response=200,
      *         description="List of favorite songs."
      *     ),
      *     @SWG\Response(
      *         response=400,
      *         description="Invalid token."
      *     )
      * )
      */
    public function favorites(Request $request){
        $user = JWTAuth::parseToken()->authenticate();

        $favorites = $this->playlistRepository->getFavorites($user,$request->page);
        $total = $this->playlistRepository->getTotalFavorites($user);

        return response()->json([
            'success'   => true,
            'meta'      => [
                'total' => $total
            ],
            'favorites' => $this->preparePlaylistSongs($favorites)
        ]);
    }

    /**
     * Get the current's user history of played songs
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function history(Request $request){
        $user = JWTAuth::parseToken()->authenticate();

        $history = $this->playlistRepository->getHistory($user, $request->page);
        $total = $this->playlistRepository->getTotalHistory($user);

        return response()->json([
            'success'   => true,
            'meta'      => [
                'total' => $total,
            ],
            'history' => $this->preparePlaylistSongs($history),
        ]);
    }

    /**
     * Update the specified playlist name.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $playlist = Playlist::find($id);

        if ($playlist) {
            if (Gate::forUser($user)->allows('playlist-edit', $playlist)) {
                $playlist->fill($request->all());
                $playlist->save();

                return response()->json([
                    'success'   => true,
                ]);
            }

            return response()->json([
                'success'   => false,
                'message'    => ['Access denied, you do not have access to update this playlist'],
            ], 403);
        }

        return response()->json([
            'success'   => false,
            'message'    => 'Playlist not found',
        ],404);
    }

    /**
     * Remove the specified playlist with all the song references.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $playlist = Playlist::find($id);

        if ($playlist) {
            if (Gate::forUser($user)->allows('playlist-delete', $playlist)) {
                // Removing the playlist and songs
                $this->playlistRepository->deleteAllSongsFromPlaylist($playlist);
                $playlist->delete();

                return response()->json([
                    'success'   => true,
                ]);
            }

            return response()->json([
                'success'   => false,
                'message'    => ['Access denied, you do not have access to remove this playlist'],
            ], 403);
        }

        return response()->json([
            'success'   => false,
            'message'    => 'Playlist not found',
        ], 404);
    }

    /**
     * Return a list of playlists without the songs,
     * only the details of each.
     */
    private function preparePlaylists($record){
        
    }

    /**
     * Returns a single playlist with their songs
     */
    private function preparePlaylist($record, $songs){
        return [
            'id'        => $record->id,
            'public'    => $record->public,
            'title'     => $record->title,
            'time'      => $record->created_at,
            'songs'     => $this->preparePlaylistSongs($songs),
        ];
    }

    /**
     * Returns an array of songs
     */
    private function preparePlaylistSongs($songs) {
        $response = [];

        foreach ($songs as $song) {
            array_push($response, $this->prepareSongItem($song));
        }

        return $response;
    }

    /**
     * Returns a single song item
     */
    private function prepareSongItem($record){
        $song = [
            'id'    => $record->id,
            'title' => $record->title,
            'lyric' => $record->lyric,
            'sound' => [
                'duration'=> $record->duration,
                'seconds'=> $record->seconds,
                'url' => $this->getFileURL((object) [
                    'file_id' => $record->audio_id,
                    'name'  => $record->audio_name,
                    'path'  => $record->audio_path,
                    'public'  => $record->audio_public,
                ]),
            ]
        ];

        if (isset($record->created_at)) {
            $song['time'] = $record->created_at;
        }

        return $song;
    }
}
