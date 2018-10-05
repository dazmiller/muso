<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Helpers\CommentsHelper;
use App\Repositories\PostRepository;
use App\Post;

/**
 * Class BlogController
 *
 * @package App\Http\Controllers\Api
 */
class BlogController extends Controller
{
    /**
     * The playlist repository instance.
     *
     * @var PlaylistRepository
     */
    protected $postRepository;

    /**
     * Create a new controller instance. This controller returns public content only
     * for blog content administration look at PostController.php
     *
     * @param  TaskRepository  $tasks
     * @return void
     */
    public function __construct(PostRepository $post)
    {
        $this->postRepository = $post;
    }

    /**
     * Returns a listing of posts.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/api/v1/blog",
     *     description="Returns the list al published posts on the blog",
     *     operationId="api.blog.index",
     *     produces={"application/json"},
     *     tags={"blog"},
     *
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
     *         description="List of posts."
     *     )
     * )
     */
    public function index(Request $request)
    {
        $posts = $this->postRepository->getLatestsPublished($request->page);
        $total = $this->postRepository->countPublishedPosts();

        return response()->json([
            'success'   => true,
            'meta'      => [
                'total'     => $total,
            ],
            'blog'    => $this->prepareData($posts)
        ]);
    }

    /**
     * Returns a single published posts.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/api/v1/blog/{id}",
     *     description="Returns a single published post on the blog, with a list of comments, asset and author information.",
     *     operationId="api.blog.index",
     *     produces={"application/json"},
     *     tags={"blog"},
     *
     *     @SWG\Parameter(
     *         description="The id of the post to retrieve",
     *         in="path",
     *         name="id",
     *         required=true,
     *         type="integer",
     *         format="int32"
     *     ),
     *
     *     @SWG\Response(
     *         response=200,
     *         description="The published post"
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="Post not found"
     *     )
     * )
     */
    public function show($id)
    {
        $post = $this->postRepository->findPublishedPost($id);

        if ($post) {
          return response()->json([
              'success'   => true,
              'meta'      => [
                  'likes'     => 50,
                  'comments'  => $post->comments->count(),
              ],
              'blog'    => $this->prepareItemData($post, $post->comments),
          ]);
        } else {
          return response()->json([
              'success'   => false,
              'message'   => 'Post not found',
          ], 404);
        }
    }

    private function prepareData($records){
        $posts = [];

        foreach ($records as $record) {
            array_push($posts,$this->prepareItemData($record));
        }

        return $posts;
    }

    private function prepareItemData($record, $comments = null){
        $json = [
            'id'    => $record->id,
            'title'    => $record->title,
            'content'    => $record->content,
            'time'    => $record->created_at,
            'allowComments'    => $record->allow_comments == 1,
            'author'=> [
                'id'    => $record->user_id,
                'name'  => $record->name,
                'image'  => $record->image,
                'about'  => $record->about,
            ]
        ];

        if($record->asset_id){
            $json['asset'] = [
                'url'   => $this->getFileURL((object)[
                    'file_id' => $record->asset_id,
                    'path'  => $record->asset_path,
                    'name'  => $record->asset_name,
                    'public'  => $record->asset_public,
                  ]),
                'type'  => $record->content_type
            ];
        }

        if($comments){
            $json['comments'] = CommentsHelper::createTree($comments);
        }

        return $json;
    }
}
