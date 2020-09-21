<?php

namespace App\Http\Controllers;

use App\Http\Helpers;
use App\Http\Resources\LikeCollection;
use App\Http\Resources\LikeResource;
use App\Post;
use App\Like;
use App\Likeable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class LikePostController extends Controller
{
    /**
     * @OA\Get(
     *     path="user/posts/{postId}/likes",
     *     summary="Get a list of likes based on postId",
     *     tags={"LikesPost"},
     *     description="Returns all likes from the system that the user has access to.",
     *     operationId="findLikesPost",
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
     *         description="Like response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Like")
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
        $post = Post::where('id', $postId)->first();

        $postLikes =  $post->likes()->paginate(10);

        return (new LikeCollection($postLikes))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Post(
     *     path="user/posts/{postId}/likes",
     *     operationId="addLike to Post",
     *     description="Creates a new Like for Post",
     *     summary="Create a new Like for a Post",
     *     tags={"LikesPost"},
     *     @OA\Response(
     *         response=200,
     *         description="Post response",
     *         @OA\JsonContent(ref="#/components/schemas/Like")
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
        $post = Post::find($postId);

        $like = Like::create([
            'user_id'           => Auth::user()->id,
            'liked_date'        => Carbon::now()->toDateTimeString(),
        ]);

        $post->likes()->attach([$like->id]);

        $postLike = $post->likes()->where('likes.id', $like->id)->first();

        if($post->user_id != Auth::user()->id){

            $notificationId = Helpers::storeNotification($post->user_id, 'like', 'post', $postId, null, $post->name);

            event(new \App\Events\NewNotification($notificationId, $post->user_id));
        }

        return (new LikeResource($postLike))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="user/posts/{postId}/likes/{likeId}",
     *     summary="Get a single Like based on the  postId and likeId",
     *     tags={"LikesPost"},
     *     description="Returns a single Like",
     *     operationId="showLike",
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
     *         name="likeId",
     *         in="path",
     *         description="id of the like",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Like response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Like")
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
    public function show($postId, $likeId)
    {
        $post = Post::where('user_id', Auth::user()->id)->where('id', $postId)->first();

        $postLike = $post->likes()->where('likes.id', $likeId)->first();

        return (new LikeResource($postLike))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Delete(
     *     path="user/posts/{postId}/likes/{likeId}",
     *     description="Delete a single Like based on the  postId",
     *     operationId="deleteLike based on the  postId",
     *     summary="Delete a Like based on the  postId",
     *     tags={"LikesPost"},
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
     *         description="likeId",
     *         in="path",
     *         name="likeId",
     *         required=true,
     *         @OA\Schema(
     *             format="int64",
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Like response",
     *         @OA\JsonContent(ref="#/components/schemas/Like")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function destroy($postId, $likeId)
    {
        $post = Post::where('id', $postId)->first();

        $postLike = $post->likes()->where('likes.id', $likeId)->first();

        Likeable::where('like_id', $likeId)->where('likeable_id', $postId)
            ->where('likeable_type', 'App\Post')->delete();

        $postLike->delete();

        return (new LikeResource($postLike))
            ->response()
            ->setStatusCode(200);
    }

    /*
    * Validation rules for saving to table
    */
    public static function validationArray():array {
        $validation = array (
            "likedDate"         => 'required',

        );

        return $validation;
    }

    /*
     * Validation messages
     */
    public static function validationMessages():array {
        $validationMessages = array (
            'likedDate.required' => 'This like date field is required',
        );
        return $validationMessages;
    }
}
