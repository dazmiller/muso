<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repositories\AlbumRepository;
use App\Activity;
use App\Album;
use App\Genre;
use JWTAuth;
use Gate;
use Validator;
use Storage;
use Config;
use Log;

class AlbumController extends Controller
{
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
    public function __construct(AlbumRepository $album)
    {
        $this->albumRepository = $album;
        $this->validations = [
            'genre_id'      => 'required',
            'title'         => 'required|min:3|max:255',
            'description'   => 'required|max:450',
            'release_date'  => 'required',
            'image'         => 'max:10000|mimes:jpeg,png'
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        if (Gate::forUser($user)->allows('albums', $user)) {
            //if is an admin and "others" param present, return all albums
            if($user->admin && $request->others){
                $records = $this->albumRepository->latests($request->page,$request->published,$request->search);
                $total   = $this->albumRepository->latestsCount($request->search,$request->published);
            }else{
                $records = $this->albumRepository->latestsByUser($user,$request->published);
                $total   = $user->albums()->count();
            }

            $albums  = [];
            foreach($records as $record){
                $album = $this->prepareAlbumData($record,[]);
                array_push($albums,$album);
            }

            return response()->json([
                'success'   => true,
                'meta'      => [
                    'total'     => $total,
                ],
                'albums'    => $albums
            ]);
        }else{
            return response()->json([
                'success'   => false,
                'errors'    => ['Not authorized to read albums'],
            ],403);
        }
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

        if (Gate::forUser($user)->allows('albums-create', $user)) {
            $validator = Validator::make($request->all(), $this->validations);

            if ($validator->fails()) {
                return response()->json([
                    'success'=> false,
                    'errors' => $validator->errors()->all()
                ],400);
            }

            $record = $user->albums()->create($request->all());
            $album = $this->prepareAlbumData($record,[]);

            if($request->hasFile('image')){
                $image  = $request->file('image');
                $this->createImage($user,$record,$image);

                $album['image'] = $this->getFileURL($record->image);
            }

            return response()->json([
                'success'   => true,
                'album'     => $album,
                'message'   => 'Album successfully created'
            ]);
        }else{
            return response()->json([
                'success'   => false,
                'errors'    => ["Access denied. You can't create albums, you need to be an author."]
            ],403);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user   = JWTAuth::parseToken()->authenticate();
        $album  = $this->albumRepository->find($id);

        if (Gate::forUser($user)->allows('albums-show', $album)) {

            $records = $this->albumRepository->findSongs($album);

            return response()->json([
                'success'   => true,
                'album'     => $this->prepareAlbumData($album, $records)
            ]);
        }else{
            return response()->json([
                'success'   => false,
                'errors'    => ["Access denied, you don't have access to this album"]
            ],403);
        }
    }

    /**
     * Display a published album, it doesn't require authentication
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function published($id)
    {
        $album  = $this->albumRepository->find($id);

        if ($album->published) {
            $records = $this->albumRepository->findSongs($album);

            return response()->json([
                'success'   => true,
                'published'     => $this->prepareAlbumData($album, $records)
            ]);
        }else{
            return response()->json([
                'success'   => false,
                'errors'    => ["Album not found"]
            ], 404);
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
        $user   = JWTAuth::parseToken()->authenticate();
        $record = $this->albumRepository->find($id);
        $createActivity = false;

        if (Gate::forUser($user)->allows('albums-update', $record)) {

            //validate
            $validator = Validator::make($request->all(), $this->validations);

            if ($validator->fails()) {
                return response()->json([
                    'success'=> false,
                    'errors' => $validator->errors()->all()
                ],400);
            }

            // Anything else other than 1 should be false
            if ($request->published != 'true') {
                $record->published = false;
            }

            //updata data
            if (!$record->published && $request->published == 'true') {
              $record->published = true;
              $record->created_at = date('Y-m-d H:i:s');
              $createActivity = true;
            }

            $record->fill($request->all());

            $record->save();
            $songs = $this->albumRepository->findSongs($record);

            $album = $this->prepareAlbumData($record, $songs);

            if($request->hasFile('image')){
                //delete old file if already exist
                //we only need one at the time
                if($record->image){
                    try {
                        $record->image->delete();
                    } catch(\Exception $error) {}
                }

                $image  = $request->file('image');

                // Make sure to upload the image to the author's folder
                $author = $user;
                if ($record->user_id != $author->id) {
                    $author = $record->author;
                }

                $img = $this->createImage($author, $record, $image);

                $album['image'] = $this->getFileURL((Object)[
                    'file_id' => $img->id,
                    'name' => $img->name,
                    'path' => $img->path,
                    'public' => true,
                ]);
            }

            if($createActivity){
                $activity = new Activity();
                $activity->fill([
                    'action'         => 'published-album',
                    'user_id'        => $record->user_id,
                    'reference_type' => 'App\\Album',
                    'reference_id'   => $record->id
                ]);
                $activity->save();
            }

            return response()->json([
                'success'   => true,
                'album'     => $album,
                'message'   => 'Album successfully updated'
            ]);
        }else{
            return response()->json([
                'success'   => false,
                'errors'    => ["Access denied, you can't update this album"]
            ],403);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){

        $user = JWTAuth::parseToken()->authenticate();
        $album = Album::find($id);

        if(Gate::forUser($user)->allows('albums-delete', $album)){

            $success = $this->albumRepository->deleteAlbum($album);

            if($success){
                return response()->json([
                    'success'   => true,
                    'message'   => 'Album successfully removed'
                ]);
            }else{
                return response()->json([
                    'success'   => false,
                    'message'   => "We can't remove this album, please contact the administrator for support. For now try to unpublish the album to make it private."
                ],500);
            }
        }else{
            return response()->json([
                'success'   => false,
                'errors'    => ["Access denied. You can't delete this album."]
            ],403);
        }
    }

    private function createImage($user,$album,$image){
        $path = Config::get('paths.album.image');
        $path = str_replace('{user_id}',$user->id,$path);
        $path = str_replace('{album_id}',$album->id,$path);
        $name = $image->getFilename().'.'.$image->getClientOriginalExtension();

        Storage::put($path.'/'.$name,  file_get_contents($image), 'public');
        return $album->image()->create([
            'name'          => $name,
            'original_name' => $image->getClientOriginalName(),
            'path'          => $path,
            'content_type'  => $image->getClientMimeType(),
            'size'          => filesize($image),
            'public'        => true,
        ]);
    }

    //Album, Songs
    private function prepareAlbumData($record, $records){
        $album = [
            'id'            => $record->id,
            'genre_id'      => $record->genre_id,
            'title'         => $record->title,
            'description'   => $record->description,
            'published'     => $record->published == 1,
            'release_date'  => $record->release_date,
        ];

        if (isset($record->author)) {
            $album['author'] = [
                'id'    => $record->author->id,
                'name'    => $record->author->name,
                'image'    => $record->author->image,
            ];
        }

        if($record->file_id){
            $album['image'] = $this->getFileURL((Object) [
                'file_id'        => $record->file_id,
                'name'      => $record->file_name,
                'path'      => $record->path,
                'public'     => $record->public,
            ]);
        }

        if(property_exists($record,'name') && property_exists($record,'user_id')){
            $album['author'] = [
                'id'        => $record->user_id,
                'name'      => $record->name,
                'image'     => $record->image,
            ];
        }

        $songs = [];
        foreach ($records as $recordSong) {
            $song = [
                'id'            => $recordSong->id,
                'title'         => $recordSong->title,
                'description'   => $recordSong->description,
                'lyric'         => $recordSong->lyric,
                'duration'      => $recordSong->duration,
                'tags'          => $recordSong->tags,
            ];

            if($recordSong->file_id){
                $song['file'] = [
                    'url'   => $this->getFileURL($recordSong),
                    'name'  => $recordSong->original_name,
                    'public'=> $recordSong->public,
                ];
                $song['sound'] = $this->getFileURL($recordSong);
            }
            array_push($songs, $song);
        }
        $album['songs'] = $songs;

        return $album;
    }
}
