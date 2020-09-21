<?php

namespace App\Http\Controllers;

use App\Commentable;
use App\Http\Resources\CommentCollection;
use App\Http\Resources\CommentResource;
use App\Post;
use App\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class CommentPostController extends Controller
{
    /**
     * @OA\Get(
     *     path="user/posts/{postId}/comments",
     *     summary="Get a list of comments based on postId",
     *     tags={"CommentsPost"},
     *     description="Returns all comments from the system that the user has access to.",
     *     operationId="findPostComments",
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="filter by Comment name text",
     *         required=false,
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(type="string"),
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="set the page number to look for. Pages are 10 items",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             format="int32"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comment response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Comment")
     *         )
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel"),
     *         @OA\MediaType(
     *             mediaType="text/json",
     *             @OA\Schema(ref="#/components/schemas/ErrorModel")
     *         )
     *     )
     * )
     *
     */
    public function index(Request $request, $postId)
    {
        $post = Post::where('user_id', Auth::user()->id)->where('id', $postId)->first();

        $postComments =  $post->comments()->get();

        // Search for summary text
        if ($request->has('search')) {
            $postComments->where('name', $request->input('search'));
        }

        return (new CommentCollection($postComments))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Post(
     *     path="user/posts/{postId}/comments",
     *     operationId="addComment to Post",
     *     description="Creates a new Comment for Post",
     *     summary="Create a new Comment for a Post",
     *     tags={"CommentsPost"},
     *     @OA\Response(
     *         response=200,
     *         description="Post response",
     *         @OA\JsonContent(ref="#/components/schemas/Comment")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function store(Request $request, $postId)
    {
        $validator = Validator::make($request->all(),  self::validationArray());

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->messages()
            ], 400);
        }

        $post = Post::find($postId);

        $comment = Comment::create([
            'user_id'           => Auth::user()->id,
            'comment_text'      => $request->commentText,
        ]);

        $post->comments()->attach([$comment->id]);

        $postComments = $post->comments()->where('comments.id', $comment->id)->first();

        return (new CommentResource($postComments))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="user/posts/{postId}/comments/{commentId}",
     *     summary="Get a single Comment based on the  postId and commentId",
     *     tags={"CommentsPost"},
     *     description="Returns a single Comment",
     *     operationId="showComment",
     *     @OA\Parameter(
     *         name="postId",
     *         in="path",
     *         description="id of the Post",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="commentId",
     *         in="path",
     *         description="id of the comment",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comment response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Comment")
     *         )
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel"),
     *         @OA\MediaType(
     *             mediaType="text/json",
     *             @OA\Schema(ref="#/components/schemas/ErrorModel")
     *         )
     *     )
     * )
     */
    public function show($postId, $commentId)
    {
        $post = Post::where('user_id', Auth::user()->id)->where('id', $postId)->first();

        $postComment = $post->comments()->where('comments.id', $commentId)->first();

        return (new CommentResource($postComment))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Put(
     *     path="user/posts/{postId}/comments/{commentId}",
     *     operationId="edit Comment based on the Post",
     *     description="Edits a Comment based on the Post",
     *     summary="Edit a Comment based on the Post",
     *     tags={"CommentsPost"},
     *     @OA\Parameter(
     *         name="postId",
     *         in="path",
     *         description="id of the Post",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="commentId",
     *         in="path",
     *         description="id of the comment",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Comment to edit",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/Comment")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comment response",
     *         @OA\JsonContent(ref="#/components/schemas/Comment")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function update(Request $request, $postId, $commentId)
    {
        $validator = Validator::make($request->all(), self::validationArray());

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->messages()
            ], 400);
        }

        $post = Post::where('user_id', Auth::user()->id)->where('id', $postId)->first();

        $postComment = $post->comments()->where('comments.id', $commentId)->first();

        $postComment->update([
            'user_id'           => Auth::user()->id,
            'comment_text'      => $request->commentText,
        ]);

        return (new CommentResource($postComment))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Delete(
     *     path="user/posts/{postId}/comments/{commentId}",
     *     description="Delete a single Comment based on the  postId",
     *     operationId="deleteComment based on the  postId",
     *     summary="Delete a Comment based on the  postId",
     *     tags={"CommentsPost"},
     *     @OA\Parameter(
     *         description="postId",
     *         in="path",
     *         name="postId",
     *         required=true,
     *         @OA\Schema(
     *             format="int64",
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="commentId",
     *         in="path",
     *         name="commentId",
     *         required=true,
     *         @OA\Schema(
     *             format="int64",
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comment response",
     *         @OA\JsonContent(ref="#/components/schemas/Comment")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function destroy($postId, $commentId)
    {
        $post = Post::where('user_id', Auth::user()->id)->where('id', $postId)->first();

        $postComment = $post->comments()->where('comments.id', $commentId)->first();

        Commentable::where('comment_id', $commentId)->where('commentable_id', $postId)
            ->where('commentable_type', 'App\Post')->delete();

        $postComment->delete();

        return (new CommentResource($postComment))
            ->response()
            ->setStatusCode(200);
    }

    /*
    * Validation rules for saving to table
    */
    public static function validationArray():array {
        $validation = array (
            "commentText"  => 'required|string|max:255|',
        );

        return $validation;
    }

    /*
     * Validation messages
     */
    public static function validationMessages():array {
        $validationMessages = array (
            'commentText.required' => 'This comment text field is required',
        );
        return $validationMessages;
    }
}
