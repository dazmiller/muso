<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Repositories\AlbumRepository;
use App\User;
use JWTAuth;

class ArtistController extends Controller
{
    /**
     * The album repository instance.
     *
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * The album repository instance.
     *
     * @var AlbumRepository
     */
    protected $albumRepository;


    /**
     * Create a new controller instance.
     *
     * @param  TaskRepository  $tasks
     * @return void
     */
    public function __construct(UserRepository $user, AlbumRepository $albumRepository)
    {
        $this->userRepository = $user;
        $this->albumRepository = $albumRepository;
    }


    public function index(Request $request){
        $onlyLatests = $request->latests != null;

        $latests = $this->userRepository->latestAuthors($request->page);
        $popular = null;
        if(!$onlyLatests){
            $popular = $this->albumRepository->topArtists(10);
        }

        return response()->json([
            'success'   => true,
            'meta'      => [
                'total'     => $this->userRepository->countAuthors(),
            ],
            'artist'   => [
                'popular'   => $popular,
                'latests'   => $latests
            ]
        ]);
    }

    /**
     * Returns all published albums by the given user
     */
    public function albums($user_id) {
        $user = User::find($user_id);

        if ($user && $user->author) {
            $albums = $this->albumRepository->getPublishedAlbumsByUser($user);
            return response()->json([
                'success'   => true,
                'albums'    => $this->prepareAlbums($albums),
            ]);
        }

        return response()->json([
            'success'   => false,
            'errors'    => ['Author not found'],
        ], 404);
    }

    private function prepareAlbums($albums) {
        $json = [];

        foreach ($albums as $album) {
            $records = $this->albumRepository->findSongs($album);
            array_push($json, $this->prepareAlbumData($album, $records));
        }

        return $json;
    }

    private function prepareAlbumData($record, $records){
        $album = [
            'id'            => $record->id,
            'genre_id'      => $record->genre_id,
            'title'         => $record->title,
            'published'     => $record->published == 1,
            'release_date'  => $record->release_date
        ];

        if($record->file_id){
            $album['image'] = $this->getFileURL((Object) [
                'file_id'        => $record->file_id,
                'name'      => $record->file_name,
                'path'      => $record->path,
                'public'     => $record->public,
            ]);
        }

        $songs = [];
        foreach ($records as $record) {
            $song = [
                'id'            => $record->id,
                'title'         => $record->title,
                'duration'      => $record->duration,
                'sound'         => $this->getFileURL($record),
                'favorites'     => $record->total_favorites,
                'comments'      => $record->comments()->count(),
                'plays'         => $record->total_plays,
                'tags'          => collect($record->tags)
                                    ->map(function ($tag) {
                                        return [
                                            'name' => $tag->name,
                                            'count'=> $tag->count,
                                        ];
                                    }),
            ];

            array_push($songs, $song);
        }
        $album['songs'] = $songs;

        return $album;
    }

}
