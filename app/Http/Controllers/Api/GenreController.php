<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repositories\GenreRepository;
use App\Repositories\SongRepository;
use JWTAuth;
use Validator;
use Gate;
use App\Genre;
use DB;
use Log;

class GenreController extends Controller
{
    
    /**
     * The album repository instance.
     *
     * @var GenreRepository
     */
    protected $genreRepository;

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
    public function __construct(GenreRepository $genre,SongRepository $song)
    {
        $this->genreRepository = $genre;
        $this->songRepository = $song;
        
        $this->validations = [
            'name'         => 'required|min:3|max:255|unique:genres'
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $genres = $this->genreRepository->all();

        return response()->json([
            'success'   => true,
            'meta'      => [
                'total' => count($genres)
            ],
            'genres'    => $this->prepareList($genres)
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user   = JWTAuth::parseToken()->authenticate();

        if (Gate::forUser($user)->allows('genres-create', '')) {
            $validator = Validator::make($request->all(), $this->validations);

            if ($validator->fails()) {
                return response()->json([
                    'success'=> false,
                    'errors' => $validator->errors()->all()
                ],400);
            }

            $genre = Genre::create($request->all());

            return response()->json([
                'success'   => true,
                'message'   => 'Genre successfully saved.',
                'genre'     => $this->prepareItem($genre)
            ]);
        }else{
            return response()->json([
                'success'   => false,
                'errors'    => ["Access denied. You can't create new genres"]
            ],403);
        }
    }

    /**
     * Display popular songs that belongs to the given genre
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $genres = $this->genreRepository->allPublished();
        $genre  = Genre::find($id);
        $songs  = $this->songRepository->publishedSongsByGenre($genre,$request->page);
        $total  = $this->songRepository->countPublishedSongsByGenre($genre);

        return response()->json([
            'success'   => true,
            'meta'      => [
                'total' => $total,
                'genres'=> $this->prepareList($genres),
                'songs' => $this->prepareSongData($songs)
            ],
            'genre'     => $this->prepareItem($genre)
        ]);
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
        $genre  = Genre::find($id);

        if (Gate::forUser($user)->allows('genres-update', $genre)) {
            $validator = Validator::make($request->all(), $this->validations);

            if ($validator->fails()) {
                return response()->json([
                    'success'=> false,
                    'errors' => $validator->errors()->all()
                ],400);
            }

            $genre->fill($request->all());
            $genre->save();

            return response()->json([
                'success'   => true,
                'message'   => 'Genre successfully updated.',
                'genre'     => $this->prepareItem($genre)
            ]);
        }else{
            return response()->json([
                'success'   => false,
                'errors'    => ["Access denied. You can't update a genre"]
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
        $user   = JWTAuth::parseToken()->authenticate();
        $genre  = Genre::find($id);

        if (Gate::forUser($user)->allows('genres-delete', $genre)) {

            try{
                DB::beginTransaction();

                $this->genreRepository->setOrphans($id); //Make orphans
                $genre->delete();

            } catch (\Exception $e){
                DB::rollBack();

                Log::debug($e);
                return response()->json([
                    'success'   => false,
                    'errors'   => ['There was an error removing this genre.']
                ],400);
            }

            DB::commit();

            return response()->json([
                'success'   => true,
                'message'   => 'Genre successfully deleted.'
            ]);
        }else{
            return response()->json([
                'success'   => false,
                'errors'    => ["Access denied. You can't delete genre."]
            ],403);
        }
    }

    private function prepareItem($record){
        if($record){
            $genre = [
                'id'    => $record->id,
                'name'  => $record->name,
                'count' => 0
            ];

            if(property_exists($record,'total')){
                $genre['count'] = $record->total;
            }
        }else{
            $genre = [];
        }

        return $genre;
    }

    private function prepareList($records){
        $genres = [];

        foreach ($records as $genre) {
            array_push($genres,$this->prepareItem($genre));
        }
        return $genres;
    }

    private function prepareSongData($records){
        $latests = [];

        foreach ($records as $record) {
            $song = [
                'id'        => $record->id,
                'title'     => $record->title,
                'plays'     => $record->total_plays,
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
                'tags'      => $record->tags,
            ];

            if($record->audio_file_id){
                $song['sound'] = $this->getFileURL((object)[
                    'file_id' => $record->audio_file_id,
                    'path'  => $record->audio_file_path,
                    'name'  => $record->audio_file_name
                  ]);
            }

            if($record->art_file_id){
                $song['album']['image'] = $this->getFileURL((object)[
                    'file_id' => $record->art_file_id,
                    'path'  => $record->art_file_path,
                    'name'  => $record->art_file_name
                  ]);
            }

            if($record->user_image){
                $song['author']['image'] = $record->user_image;
            }

            array_push($latests, $song);
        }

        return $latests;
    }
}
