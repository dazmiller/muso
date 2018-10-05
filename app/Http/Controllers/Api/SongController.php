<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Helpers\CommentsHelper;
use App\Http\Helpers\MP3Helper;
use App\Http\Controllers\Controller;
use App\Repositories\AlbumRepository;
use App\Repositories\PlaylistRepository;
use App\Repositories\SongRepository;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Album;
use App\File;
use App\Song;
use App\Activity;
use JWTAuth;
use Validator;
use Storage;
use Config;
use Gate;
use Log;

class SongController extends Controller
{
    /**
     * The album repository instance.
     *
     * @var AlbumRepository
     */
    protected $albumRepository;

    /**
     * The song repository instance.
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
    public function __construct(AlbumRepository $album, SongRepository $song, PlaylistRepository $playlist)
    {
        $this->albumRepository = $album;
        $this->playlistRepository = $playlist;
        $this->songRepository = $song;

        $this->validations = [
            'title'         => 'required|min:3|max:255',
            'album_id'      => 'required',
            'description'   => 'max:450',
            'lyric'         => 'max:1500',
        ];
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $user = JWTAuth::parseToken()->authenticate();
      $album = $this->albumRepository->find($request->album_id);

      if (Gate::forUser($user)->allows('albums-update', $album)) {
          //1.- Validate fields
          $this->validations['audio'] = 'required|mimes:mpga';

          $validator = Validator::make($request->all(), $this->validations);

          if ($validator->fails()) {
              return response()->json([
                  'success'=> false,
                  'errors' => $validator->errors()->all()
              ],400);
          }

          //3.- Add song to the album and save
          $file  = $request->file('audio');
          $duration = $this->getSongDuration($file);
          
          $record = new Song();
          $record->fill($request->all());
          $record->album_id = $album->id;
          $record->duration = $duration;
          $record->save();

          $this->uploadSong($user, $album->id, $record, $file, $duration);

          // 4.- Add tags if any
          if ($request->input('tags')) {
            $record->tag($request->input('tags'));
          }

          $song = $this->prepareSongData($record,$record->file);

          return response()->json([
              'success'   => true,
              'message'   => 'Song successfully added',
              'song'      => $song
          ]);
      }

      return response()->json([
          'success'   => false,
          'errors'    => ['Not authorized to add songs to this album'],
      ],403);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        $record = $this->songRepository->findPublished($id);

        if($record){
            $song = $this->prepareSongData($record,$record,$record->comments);
            

            return response()->json([
                'success'   => true,
                'song'      => $song
            ]);
        }else{
            return response()->json([
                'success'   => false,
                'errors'    => ['Song not found']
            ],404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //1.- Validate fields
        $this->validations['file'] = 'mimes:mpga';
        $validator = Validator::make($request->all(), $this->validations);

        if ($validator->fails()) {
            return response()->json([
                'success'=> false,
                'errors' => $validator->errors()->all()
            ],400);
        }

        //2.- Check ownership
        $user   = JWTAuth::parseToken()->authenticate();
        $album = $this->albumRepository->find($request->album_id);

        if (Gate::forUser($user)->allows('albums-update', $album)) {
            $record = $this->albumRepository->findSongInAlbum($request->album_id, $request->id);

            if ($record) {
                //3.- Update the data
                $duration = null;
                if ($request->hasFile('audio')) {
                    $duration = $this->getSongDuration($request->file('audio'));
                    $record->duration = $duration;
                }
                $record->title = $request->title;
                $record->description = $request->description;
                $record->lyric = $request->lyric;
                $record->save();

                if($request->hasFile('audio')){
                    //delete old file if already exist
                    //we only need one at the time
                    if($record->file){
                        $record->file->delete();
                    }

                    $file  = $request->file('audio');
                    $author = $user;
                    if ($author->id != $album->user_id) {
                        $author = $album->author;
                    }
                    $this->uploadSong($author, $request->album_id, $record, $file, $duration);
                }

                // 4.- Update tags if any
                if ($request->input('tags')) {
                    $record->setTags($request->input('tags'));
                } else {
                    $record->untag();
                }

                $song = $this->prepareSongData($record, $record->file);

                return response()->json([
                    'success'   => true,
                    'message'   => 'Song successfully updated',
                    'song'      => $song
                ]);
            }

             return response()->json([
                'success'   => false,
                'errors'    => ['Song nto found']
            ], 404);
        }else{
            return response()->json([
                'success'   => false,
                'errors'    => ['You are not authorized to update songs in this album']
            ], 403);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $user   = JWTAuth::parseToken()->authenticate();
        $song = Song::find($request->id);
        $album = Album::find($song->album_id);

        // Only owner of the album (or admin) can remove a song from an album
        if (Gate::forUser($user)->allows('albums-update', $album)) {
            // It might be the case the song doesn't have a file
            if ($song->file) {
                $song->file->delete();
            }
            $this->playlistRepository->deleteSongFromPlaylists($song->id);
            $song->untag();
            $song->delete();

            return response()->json([
                'success'   => true,
                'message'   => 'Song successfully removed'
            ]);
        } else {
            return response()->json([
                'success'   => false,
                'error'     => 'Access denied, you can not remove the song from this album'
            ], 403);
        }
    }

    public function likeable(Request $request, $id){
        $user   = JWTAuth::parseToken()->authenticate();
        $song   = Song::find($id);

        if($song->album->published){

            if($song->liked($user->id)){
                $song->unlike($user->id);
                //Decrement favorites counter
                $song->total_favorites -= 1;
                $song->save();
                $message = 'You unliked this song';
                $action  = 'unlike';

            }else{
                $song->like($user->id);
                //Increment favorites counter
                $song->total_favorites += 1;
                $song->save();
                $message = 'You liked this song';
                $action  = 'like';
            }

            $activity = new Activity();
            $activity->fill([
                'action'         => $action,
                'user_id'        => $user->id,
                'reference_type' => 'App\\Song',
                'reference_id'   => $song->id
            ]);
            $activity->save();


            return response()->json([
                'success'   => true,
                'song'      => $song,
                'message'   => $message
            ]);
        }else{
            return response()->json([
                'success'   => false,
                'error'     => 'Song not found'
            ],404);
        }
    }

    private function uploadSong($user, $albumId, $song, $file, $duration = '00:00:00'){
        $path = Config::get('paths.album.track');
        $path = str_replace('{user_id}', $user->id, $path);
        $path = str_replace('{album_id}',$albumId,$path);
        $name = $file->getFilename().'.'.$file->getClientOriginalExtension();

        Storage::put($path.'/'.$name,  file_get_contents($file), 'public');

        $record = new File();
        $record->fill([
            'name'          => $name,
            'original_name' => $file->getClientOriginalName(),
            'path'          => $path,
            'content_type'  => $file->getClientMimeType(),
            'size'          => filesize($file),
            'duration'      => $duration,
        ]);
        $record->fileable_type = Song::class;
        $record->fileable_id = $song->id;
        $record->public = false;
        $record->save();

        return $record;
    }

    private function prepareSongData($record, $file, $comments = null){

        $data = $record->getAttributes();
        try{
            $user   = JWTAuth::parseToken()->authenticate();
        }catch(JWTException $e){
            $user = false;
        }
        $song = [
            'id'            => $record->id,
            'title'         => $record->title,
            'description'   => $record->description,
            'lyric'         => $record->lyric,
            'isFavorite'    => false,
            'tags'          => collect($record->tags)
                                ->map(function ($tag) {
                                    return [
                                        'name' => $tag->name,
                                        'count'=> $tag->count,
                                    ];
                                }),
        ];


        if($user){
            $song['isFavorite'] = $record->liked($user->id);
        }

        if(isset($file->original_name)){
            // This is the response for the administration views
            $song['file'] = [
                'url'       => $this->getFileURL((object) [
                    'file_id' => $file->id,
                    'name'  => $file->original_name,
                    'path'  => $file->path,
                    'public'=> $file->public,
                ]),
                'name'      => $file->original_name
            ];
            $song['video'] = [
                'duration'  => $record->duration,
                'seconds'   => $record->seconds,
                'url'       => $this->getFileURL((object) [
                    'file_id' => $file->video_id,
                    'name'  => $file->video_file_name,
                    'path'  => $file->video_file_path,
                    'public'  => $file->video_file_public,
                ])
            ];
        }else{
            // This response is sending to the public views
            $song['sound'] = [
                'duration'  => $record->duration,
                'seconds'   => $record->seconds,
                'url'       => $this->getFileURL((object) [
                    'file_id' => $file->audio_id,
                    'name'  => $file->audio_file_name,
                    'path'  => $file->audio_file_path,
                    'public'  => $file->audio_file_public,
                ])
            ];

            $song['video'] = [
                'duration'  => $record->duration,
                'seconds'   => $record->seconds,
                'url'       => $this->getFileURL((object) [
                    'file_id' => $file->video_id,
                    'name'  => $file->video_file_name,
                    'path'  => $file->video_file_path,
                    'public'  => $file->video_file_public,
                ])
            ];

        }

        if (isset($data['album_title'])) {

            $song['plays'] = $record->total_plays;
            $song['favorites'] = $record->total_favorites;
            $song['downloads'] = $record->total_downloads;

            $song['album'] = [
                'id'        => $record->album_id,
                'title'     => $record->album_title,
                'released'  => $record->release_date,
                'image'     => $this->getFileURL((object) [
                  'file_id' => $file->art_id,
                  'name'  => $file->art_file_name,
                  'path'  => $file->art_file_path,
                  'public'  => $file->art_file_public,
                ])
            ];

            $song['author'] = [
                'id'        => $record->user_id,
                'name'      => $record->name,
                'about'      => $record->about,
                'image'      => $record->image,
            ];
        }

        if($comments){
            $song['comments'] = CommentsHelper::createTree($comments);
        }

        return $song;
    }

    private function getSongDuration($file) {
        // Get the mp3 duration
        try {
            $mp3 = new MP3Helper($file->path());
            $duration = $mp3->getDurationEstimate();
            $duration = MP3Helper::formatTime($duration);
        } catch (Exception $e) {
            $duration = '00:00:00';
        }

        return $duration;
    }
}
