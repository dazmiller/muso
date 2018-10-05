<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repositories\AlbumRepository;
use App\Repositories\SongRepository;
use App\Http\Serializers\UserSerializer;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Album;
use App\Genre;
use App\Song;
use App\User;
use JWTAuth;
use DB;
use Log;

class DiscoverController extends Controller
{
    /**
     * The album repository instance.
     *
     * @var AlbumRepository
     */
    protected $albumRepository;
    
    /**
     * The album repository instance.
     *
     * @var SongRepository
     */
    protected $songRepository;

    /**
     * Create a new controller instance.
     *
     * @param  TaskRepository  $tasks
     * @return void
     */
    public function __construct(AlbumRepository $album, SongRepository $song, UserSerializer $userSerializer)
    {
        $this->albumRepository = $album;
        $this->songRepository = $song;
        $this->userSerializer = $userSerializer;
    }

    public function index(){
        $latests = $this->prepareSongData($this->albumRepository->latestsPublishedSongs(8));
        $random  = $this->prepareSongData($this->albumRepository->randomPublishedSongs(12));
        $topten  = $this->prepareSongData($this->albumRepository->topTenPublishedSongs());
        $artists = $this->albumRepository->topArtists(5);

        return response()->json([
            'success'   => true,
            'discover'  => [
                'latests'           => $latests,
                'recommendations'   => $random,
                'topten'            => $topten,
                'artists'           => $artists
            ]
        ]);
    }

    /**
     * Returns a list of published songs by tag
     */
    public function tag(Request $request, $tag) {
        $songs = $this->songRepository->publishedSongsByTag($tag, $request->input('page'), $request->input('popular'));

        return response()->json([
            'success'   => true,
            'meta'      => [
                'tag'       => $tag,
                'total'     => 0,
            ],
            'songs'     => $this->prepareSongData($songs),
        ]);
    }

    /**
     * Returns a list of published songs by genre
     */
    public function genre(Request $request, $genre_id) {
        $genre = Genre::find($genre_id);
        $songs = $this->songRepository->publishedSongsByGenre($genre, $request->input('page'), $request->input('popular'));
        $total = $this->songRepository->countPublishedSongsByGenre($genre);

        return response()->json([
            'success'   => true,
            'meta'      => [
                'total' => $total,
            ],
            'songs'     => $this->prepareSongData($songs),
        ]);
    }

    /**
     * Returns the list of recent users who liked the giving song
     */
    public function liked(Request $request, $song_id) {
        $song = Song::find($song_id);

        // Check if the song exist
        if ($song) {
            // Check if the album is published
            if ($song->album->published) {
                $users = User::usersWhoLikedSong($song->id)->get();
                return response()->json([
                    'success'   => true,
                    'users'     => $this->userSerializer->list($users, ['basic']),
                ]);
            }
        }

        return response()->json([
            'success'   => false,
            'messages'  => [
                'Song not found',
            ],
        ], 404);
    }

    private function prepareSongData($records){
        try{
            $user   = JWTAuth::parseToken()->authenticate();
        }catch(JWTException $e){
            $user = false;
        }

        $latests = [];

        foreach ($records as $record) {
            $song = [
                'id'        => $record->id,
                'title'     => $record->title,
                'playsCount'=> $record->total_plays,
                'plays'=> $record->plays,
                'duration'  => $record->duration,
                'isFavorite'=> false,
                'favorites' => $record->total_favorites,
                'comments'  => $record->comments()->count(),
                'time'      => $record->created_at,
                'author'    => [
                    'id'    => $record->user_id,
                    'name'  => $record->name
                ],
                'album'     => [
                    'id'    => $record->album_id
                ],
                'tags'      => collect($record->tags)
                                ->map(function ($tag) {
                                    return [
                                        'name' => $tag->name,
                                        'count'=> $tag->count,
                                    ];
                                }),
            ];

            if($record->audio_file_id){
                $song['sound'] = $this->getFileURL((object) [
                  'file_id' => $record->audio_file_id,
                  'name' => $record->audio_file_name,
                  'path' => $record->audio_file_path,
                  'public' => isset($record->audio_file_public) ? $record->audio_file_public : $record->public,
                ]);
            }

            if($record->art_file_id){
                $song['album']['image'] = $this->getFileURL((object) [
                  'file_id' => $record->art_file_id,
                  'name' => $record->art_file_name,
                  'path' => $record->art_file_path,
                  'public' => isset($record->art_file_public) ? $record->art_file_public : $record->public,
                ]);
            }

            if($record->user_image){
                $song['author']['image'] = $record->user_image;
            }

            if($user){
                $song['isFavorite'] = $record->liked($user->id);
            }

            array_push($latests, $song);
        }

        return $latests;
    }
}
