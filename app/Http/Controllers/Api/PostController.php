<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Repositories\PostRepository;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Post;
use App\Activity;
use Carbon\Carbon;
use JWTAuth;
use Gate;
use Validator;
use Storage;
use Config;
use Log;

class PostController extends Controller
{
    /**
     * The playlist repository instance.
     *
     * @var PlaylistRepository
     */
    protected $postRepository;

    /**
     * Create a new controller instance.
     *
     * @param  TaskRepository  $tasks
     * @return void
     */
    public function __construct(PostRepository $post)
    {
        $this->postRepository = $post;

        $this->validations = [
            'title'         => 'required|min:3|max:255',
            'content'       => 'required',
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user   = JWTAuth::parseToken()->authenticate();

        if (Gate::forUser($user)->allows('blog',null)) {

            if($user->admin && $request->all){
                $options = [
                    'drafts'=> $request->drafts,
                    'page'  => $request->page,
                    'search'=> $request->search
                ];

                $posts = $this->postRepository->getAllPosts($options);
                $total = $this->postRepository->countAllPosts($options);

            }else{
                //get posts for the current author
                $posts = [];
                $total=0;
            }

            return response()->json([
                'success'   => true,
                'meta'      => [
                    'total' => $total
                ],
                'posts'     => $this->prepareData($posts)
            ]);
        }else{
            return response()->json([
                'success'   => false,
                'errors'    => ['Only authors can access this resource.']
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
        $user   = JWTAuth::parseToken()->authenticate();

        if (Gate::forUser($user)->allows('blog-create',null)) {
            $validator = Validator::make($request->all(), $this->validations);

            if ($validator->fails()) {
                return response()->json([
                    'success'=> false,
                    'errors' => $validator->errors()->all()
                ],400);
            }

            $record = new Post();
            $record->fill($request->all());
            $record->author_id = $user->id;
            $record->allow_comments = $request->input('allow_comments') == 'true';
            $record->published = $request->input('published') == 'true';
            $record->save();

            $post = $this->prepareItemData($record,$user);

            if($request->hasFile('asset')){
                $asset  = $request->file('asset');
                $newFile = $this->createAsset($user, $record, $asset);

                $post['asset'] = [
                    'url'   => $this->getFileURL((object)[
                        'file_id' => $newFile->id,
                        'path'  => $newFile->path,
                        'name'  => $newFile->name
                    ]),
                    'type'  => $newFile->content_type
                ];
            }

            return response()->json([
                'success'   => true,
                'message'   => 'Your post has been created.',
                'post'      => $post
            ]);

        }else{
            return response()->json([
                'success'   => false,
                'errors'    => ['Only authors can access this resource.']
            ],403);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $user   = JWTAuth::parseToken()->authenticate();
        $post   = Post::find($id);

        if (Gate::forUser($user)->allows('blog-show', $post)) {
            $json = $this->prepareItemData($post, $post->author);

            if($post->asset){
                $json['asset'] = [
                    'url'   => $this->getFileURL((object)[
                        'file_id' => $post->asset->id,
                        'path'  => $post->asset->path,
                        'name'  => $post->asset->name,
                        'public'=> $post->asset->public,
                    ]),
                    'type'  => $post->asset->content_type
                ];
            }

            return response()->json([
                'success'   => true,
                'post'      => $json
            ]);

        }else{
            return response()->json([
                'success'   => false,
                'errors'    => ['You are not the author of this post.']
            ],403);
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
        $post   = Post::find($id);
        $createActivity = false;

        if (Gate::forUser($user)->allows('blog-update', $post)) {
            $validator = Validator::make($request->all(), $this->validations);

            if ($validator->fails()) {
                return response()->json([
                    'success'=> false,
                    'errors' => $validator->errors()->all()
                ],400);
            }

            $post->fill($request->all());
            $post->allow_comments = $request->allow_comments == 'true';

            if($post->published == false && $request->published == 'true'){
                $post->published    = true;
                $post->created_at   = Carbon::now();

                $createActivity = true;
            }else{
                $post->published = $request->published == 'true';
            }

            $post->save();
            $json = $this->prepareItemData($post,$post->author);

            if($request->hasFile('asset')){
                //delete old file if already exist
                //we only need one at the time
                if($post->asset){
                    $post->asset->delete();
                }

                // Make sure to upload the image to the author's folder
                $author = $user;
                if ($post->user_id != $author->id) {
                    $author = $post->author;
                }

                $asset  = $request->file('asset');
                $newFile = $this->createAsset($author, $post, $asset);

                $json['asset'] = [
                    'url'   => $this->getFileURL((object)[
                        'file_id' => $newFile->id,
                        'path'  => $newFile->path,
                        'name'  => $newFile->name
                    ]),
                    'type'  => $newFile->content_type
                ];
            }

            if($createActivity){
                $activity = new Activity();
                $activity->fill([
                    'action'         => 'published-post',
                    'user_id'        => $post->author_id,
                    'reference_type' => 'App\\Post',
                    'reference_id'   => $post->id
                ]);
                $activity->save();
            }

            return response()->json([
                'success'   => true,
                'message'   => 'Your post has been updated.',
                'post'      => $json
            ]);

        }else{
            return response()->json([
                'success'   => false,
                'errors'    => ['You are not the author of this blog post.']
            ],403);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user   = JWTAuth::parseToken()->authenticate();
        $post   = Post::find($id);

        if (Gate::forUser($user)->allows('blog-delete',$post)) {

            if($post->asset){
                $post->asset->delete();
            }
            $post->delete();

            return response()->json([
                'success'   => true,
                'message'   => 'Your post has been deleted.'
            ]);

        }else{
            return response()->json([
                'success'   => false,
                'errors'    => ['You are not the author of this blog post.']
            ],403);
        }
    }

    private function createAsset($user, $post, $asset){
        $path = Config::get('paths.post.asset');
        $path = str_replace('{user_id}',$user->id,$path);
        $path = str_replace('{post_id}',$post->id,$path);
        $name = $asset->getFilename().'.'.$asset->getClientOriginalExtension();

        Storage::put($path.'/'.$name,  file_get_contents($asset), 'public');
        return $post->asset()->create([
            'name'          => $name,
            'original_name' => $asset->getClientOriginalName(),
            'path'          => $path,
            'content_type'  => $asset->getClientMimeType(),
            'size'          => filesize($asset)
        ]);
    }

    private function prepareData($records){
        $posts = [];

        foreach ($records as $record) {
            $post = $this->prepareItemData($record,$record);

            array_push($posts,$post);
        }
        return $posts;
    }

    private function prepareItemData($record, $author){
        $post = [
            'id'                => $record->id,
            'title'             => $record->title,
            'content'           => $record->content,
            'published'         => $record->published == 1,
            'allow_comments'    => $record->allow_comments == 1,
            'time'              => $record->created_at,
            'author'            => [
                'name'  => $author->name,
                'image' => $author->image,
            ]
        ];

        if($author->user_id){
            $post['author']['id'] = $author->user_id;
        }else{
            $post['author']['id'] = $author->id;
        }

        return $post;
    }
}
