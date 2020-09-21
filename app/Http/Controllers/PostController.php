<?php

namespace App\Http\Controllers;

use App\Likeable;
use App\Mediable;
use App\Post;
use App\Http\Resources\PostCollection;
use App\Http\Resources\PostResource;
use App\PostProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * @OA\Get(
     *     path="/user/posts",
     *     summary="Get a list of posts",
     *     tags={"Posts"},
     *     description="Returns all  posts from the system that the user has access to.",
     *     operationId="findPosts",
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="filter by name text",
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
     *         description="Post response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Post")
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
    public function index(Request $request, $profileId)
    {
        $post = Post::where('profile_id', $profileId)
            ->orderBy('id', 'desc')
            ->paginate(10);

        // Search for summary text
        if ($request->has('search')) {
            $post->where('name', $request->input('search'));
        }
        return ( new PostCollection($post))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Post(
     *     path="/user/posts",
     *     operationId="addPost",
     *     description="Creates a new Post for user",
     *     summary="Create a new Post for a user",
     *     tags={"Posts"},
     *     @OA\Response(
     *         response=200,
     *         description="Post response",
     *         @OA\JsonContent(ref="#/components/schemas/Post")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),  self::validationArray());

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->messages()
            ], 400);
        }

        $post = Post::create([
            'user_id'               => Auth::user()->id,
            'profile_id'            => $request->profileId,
            'group_id'              => $request->groupId,
            'project_id'            => $request->projectId,
            'summary'               => $request->summary,
            'description'           => 'LOC POST',
            'image_uri'             => $request->file('imageUri') ? $request->file('imageUri')->hashName(): '',
        ]);

//        $createdByArray = json_decode($request->createdByArray);
//
//        foreach ($createdByArray as $item){
//            PostProfile::create(['post_id' => $post->id, 'profile_id' => $item->id]);
//        }

        return (new PostResource($post))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="/user/posts/{postId}",
     *     summary="Get a single post",
     *     tags={"Posts"},
     *     description="Returns a single post",
     *     operationId="showPost",
     *     @OA\Parameter(
     *         name="postId",
     *         in="path",
     *         description="id of the post",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Post")
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
    public function show($postId)
    {
//        $post = Post::where('profile_id', $profileId)
//            ->where('id', $postId)->first();
        $post = Post::where('id', $postId)->first();

        return ( new PostResource($post))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Put(
     *     path="/user/posts/{postId}",
     *     operationId="editPost",
     *     description="Edits a post",
     *     summary="Edit a post",
     *     tags={"Posts"},
     *     @OA\Parameter(
     *         name="postId",
     *         in="path",
     *         description="id of the postId",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Post to edit",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/Post")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post response",
     *         @OA\JsonContent(ref="#/components/schemas/Post")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function update(Request $request, $postId)
    {
        $validator = Validator::make($request->all(), self::validationArray());

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->messages()
            ], 400);
        }

        $post = Post::where('user_id', Auth::user()->id)->where('id', $postId)
            ->first();

        $post->update([
            'user_id'               => Auth::user()->id,
            'profile_id'            => $request->profileId,
            'project_id'            => $request->projectId,
            'group_id'              => $request->groupId,
            'summary'               => $request->summary,
            'description'           => 'LOC POST',

        ]);


        if($request->createdBy){

            $post->update(['created_by' => $request->createdBy]);
        }


        if(isset($request->images) && !empty($request->images)){
            $imagesArray = json_decode($request->images);

            $imagesIds = [];
            foreach ($imagesArray as $item){
                array_push($imagesIds, $item->id);
            }

            $post->media()->where('media_type', 0)->whereNotIn('media.id', $imagesIds)->delete();

        }


        if(isset($request->urlDelete) && !empty($request->urlDelete)){
            $urlArray = json_decode($request->urlDelete);

            $urlIds = [];
            foreach ($urlArray as $item){
                array_push($urlIds, $item->id);
            }

            $post->media()->where('media_type', 5)->whereNotIn('media.id', $urlIds)->delete();
        }


//        $createdByArray = json_decode($request->createdByArray);
//
//        foreach ($createdByArray as $item){
//            PostProfile::create(['post_id' => $post->id, 'profile_id' => $item->id]);
//        }



        return ( new PostResource($post))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Delete(
     *     path="/user/posts/{postId}",
     *     description="Delete a single Post based on the  postId",
     *     operationId="deletePost",
     *     summary="Delete a post",
     *     tags={"Posts"},
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
     *     @OA\Response(
     *         response=200,
     *         description="Post response",
     *         @OA\JsonContent(ref="#/components/schemas/Post")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function destroy($postId)
    {
//        $post = Post::where('user_id', Auth::user()->id)->where('id', $postId)
//            ->where('profile_id', $profileId)
//            ->first();
        $post = Post::where('user_id', Auth::user()->id)
            ->where('id', $postId)
            ->first();

        $post->media()->delete();

        Mediable::where('mediable_id', $postId)
            ->where('mediable_type', '=', 'App\Post')
            ->delete();

        $post->likes()->delete();

        Likeable::where('likeable_id', $postId)
            ->where('likeable_type', '=', 'App\Post')
            ->delete();

        $postImages = $post->media()->where('media_type', 0)->get();

        if(!empty($postImages->toArray())){

            foreach ($postImages as $image){
               Storage::disk('public')->delete('posts/mediaPostImage/media/'.$image->id, $image->uri);
            }
        }

        $post->delete();

        return ( new PostResource($post))
            ->response()
            ->setStatusCode(200);
    }

    public function updateShareCount(Request $request, $postId)
    {

        Post::where('user_id', Auth::user()->id)
            ->where('id', $postId)
            ->update(['share_count' => $request->shareCount]);

        $post = Post::where('id', $postId)->first();
        return (new PostResource($post))
            ->response()
            ->setStatusCode(200);
    }


    public function pinToTop(Request $request)
    {
        Post::where('id', $request->id)->update(['pin_to_top' => 1]);
    }

    /*
     * Validation rules for saving to table
     */
    public static function validationArray():array {
        $validation = array (
            "summary" => 'required|string|max:255',
        );

        return $validation;
    }

    /*
     * Validation messages
     */
    public static function validationMessages():array {
        $validationMessages = array (
            'summary.required' => 'This name field is required',
        );
        return $validationMessages;
    }
}
