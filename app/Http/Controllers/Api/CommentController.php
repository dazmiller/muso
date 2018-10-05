<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repositories\CommentRepository;
use App\Song;
use App\Comment;
use App\Post;
use App\Activity;
use Gate;
use JWTAuth;
use Validator;

class CommentController extends Controller
{
    /**
     * The comment repository instance.
     *
     * @var CommentRepository
     */
    protected $commentRepository;

    /**
     * Create a new controller instance.
     *
     * @param  TaskRepository  $tasks
     * @return void
     */
    public function __construct(CommentRepository $comment)
    {
        $this->commentRepository = $comment;

        $this->validations = [
            'model'         => 'required|in:song,post',
            'model_id'      => 'required',
            'title'         => 'nullable|min:3|max:255',
            'body'          => 'required|min:3',
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

        if (Gate::forUser($user)->allows('comments', '')) {

            $comments = $this->commentRepository->getAllComments($request->page);

            $total = Comment::count();

            return response()->json([
                'success'   => true,
                'meta'      => [
                    'total' => $total
                ],
                'comments'  => $this->prepareData($comments)
            ]);
        }else{
            return response()->json([
                'success'   => false,
                'errors'    => ['Only administrators can access this resource']
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
        $model  = null;
        $parent = null;

        if (Gate::forUser($user)->allows('comments-create', '')) {

            $validator = Validator::make($request->all(), $this->validations);

            $validator->after(function($validator) use ($request){
                $this->getCommentable($request,$validator);
            });

            if ($validator->fails()) {
                return response()->json([
                    'success'=> false,
                    'errors' => $validator->errors()->all()
                ],400);
            }

            $model = $this->getCommentable($request,$validator);

            if($request->has('parent_id')){
                $parent = Comment::find($request->parent_id);
                if(!$parent){
                    return response()->json([
                        'success'=> false,
                        'errors' => ['Parent comment not found']
                    ],400);
                }
            }

            $comment = new Comment();
            $comment->title     = $request->title;
            $comment->body      = $request->body;
            $comment->user_id   = $user->id;
            $comment->published = true;

            if($parent){
                $comment->parent_id = $parent->id;
            }

            $model->comments()->save($comment);

            $modelType = '';
            if($request->model == 'song'){
                $modelType = 'App\\Song';
            }

            if($request->model == 'post'){
                $modelType = 'App\\Post';
            }

            $activity = new Activity();
            $activity->fill([
                'action'         => 'comment',
                'user_id'        => $user->id,
                'reference_type' => $modelType,
                'reference_id'   => $model->id
            ]);
            $activity->save();

            return response()->json([
                'success'   => true,
                'message'   => 'Thanks for leaving a comment.',
                'comment'   => $this->prepareDataItem($user, $comment)
            ]);
        }else{
            return response()->json([
                'success'   => false,
                'errors'    => ['You need to login in order to leave comments']
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
        $comment= Comment::find($id);

        if (Gate::forUser($user)->allows('comments-show', $comment)) {
            return response()->json([
                'success'   => true,
                'comment'   => $this->prepareDataItem($comment->user, $comment)
            ]);
        }else{
            return response()->json([
                'success'   => false,
                'errors'    => ['You need to login in order to leave comments']
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
        $comment= Comment::find($id);

        if (Gate::forUser($user)->allows('comments-update', $comment)) {

            $validator = Validator::make($request->all(), $this->validations);

            if ($validator->fails()) {
                return response()->json([
                    'success'=> false,
                    'errors' => $validator->errors()->all()
                ],400);
            }

            $comment->fill($request->all());
            $comment->published = $request->published == 'true';
            $comment->save();

            return response()->json([
                'success'   => true,
                'comment'   => $this->prepareDataItem($comment->user,$comment)
            ]);
        }else{
            return response()->json([
                'success'   => false,
                'errors'    => ['Only administrators can access this resource']
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
        $comment= Comment::find($id);

        if (Gate::forUser($user)->allows('comments-delete', $comment)) {

            $comment->delete();

            return response()->json([
                'success'   => true,
                'message'   => 'The comment has been removed successfully'
            ]);
        }else{
            return response()->json([
                'success'   => false,
                'errors'    => ['Only administrators can access this resource']
            ],403);
        }
    }

    private function getCommentable($request, $validator){
        $model = null;
        $error = '`model` needs to be `song` or `post`';

        if($request->model == 'song'){
            $model = Song::find($request->model_id);
            // @TODO: individual songs should also be published, not only
            // the albumn, but each song.
            // if ($model != null && !$model->published) {
            //
            // }
            $error = 'Song not found';
        }

        if($request->model == 'post'){
            $model = Post::find($request->model_id);

            // Validate if post is unpublished, then
            // user should not be able to add a comment
            if ($model != null && !$model->published) {
              $model = null;
            }
            $error = 'Post not found';
        }

        if ($model == null) {
            $validator->errors()->add('comment', $error);
        }

        return $model;
    }

    private function prepareData($records){
        $comments = [];

        foreach ($records as $comment) {
            array_push($comments,[
                'id'        => $comment->id,
                'title'     => $comment->title,
                'body'      => $comment->body,
                'published' => $comment->published,
                'time'      => $comment->created_at,
                'author'    => [
                    'id'    => $comment->user_id,
                    'name'  => $comment->name,
                    'image' => $comment->image
                ]
            ]);
        }

        return $comments;
    }

    private function prepareDataItem($user, $comment){
        $currentUser   = JWTAuth::parseToken()->authenticate();

        $json = [
            'id'        => $comment->id,
            'parent_id' => $comment->parent_id,
            'title'     => $comment->title,
            'body'      => $comment->body,
            'time'      => $comment->created_at,
            'author'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'image' => $user->image
            ]
        ];

        if($currentUser && $currentUser->admin){
            $json['published'] = $comment->published == 1;
            $json['model'] = $comment->commentable_type;
            $json['model_id'] = $comment->commentable_id;
        }

        return $json;
    }
}
