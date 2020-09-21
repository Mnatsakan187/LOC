<?php

namespace App\Http\Controllers;

use App\Http\Resources\TagCollection;
use App\Http\Resources\TagResource;
use App\Post;
use App\Tag;
use App\Taggable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class TagPostController extends Controller
{
    /**
     * @OA\Get(
     *     path="user/posts/{postId}/tags",
     *     summary="Get a list of tags based on postId",
     *     tags={"TagsPost"},
     *     description="Returns all tags from the system that the user has access to.",
     *     operationId="findTagsPost",
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="filter by Tag name text",
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
     *         description="Tag response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Tag")
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

        $postTags =  $post->tags()->paginate(10);

        // Search for summary text
        if ($request->has('search')) {
            $postTags->where('name', $request->input('search'));
        }

        return (new TagCollection($postTags))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Post(
     *     path="user/posts/{postId}/tags",
     *     operationId="addTag to Post",
     *     description="Creates a new Tag for Post",
     *     summary="Create a new Tag for a Post",
     *     tags={"TagsPost"},
     *     @OA\Response(
     *         response=200,
     *         description="Post response",
     *         @OA\JsonContent(ref="#/components/schemas/Tag")
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

        $tag = Tag::create([
            'user_id'         => Auth::user()->id,
            'name'            => $request->name,
            'description'     => $request->description,
            'rbg_color_code'  => $request->rbgColorCode,
            'created_by'      => $request->createdBy,
        ]);

        $post->tags()->attach([$tag->id]);

        $postTags = $post->tags()->first();

        return (new TagResource($postTags))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="user/posts/{postId}/tags/{tagId}",
     *     summary="Get a single Tag based on the  postId and tagId",
     *     tags={"TagsPost"},
     *     description="Returns a single Tag",
     *     operationId="showTag",
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
     *         name="tagId",
     *         in="path",
     *         description="id of the tag",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tag response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Tag")
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
    public function show($postId, $tagId)
    {
        $post = Post::where('user_id', Auth::user()->id)->where('id', $postId)->first();

        $postTag = $post->tags()->where('tags.id', $tagId)->first();

        return (new TagResource($postTag))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Put(
     *     path="user/posts/{postId}/tags/{tagId}",
     *     operationId="edit Tag based on the Post",
     *     description="Edits a Tag based on the Post",
     *     summary="Edit a Tag based on the Post",
     *     tags={"TagsPost"},
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
     *         name="tagId",
     *         in="path",
     *         description="id of the tag",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Tag to edit",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/Tag")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tag response",
     *         @OA\JsonContent(ref="#/components/schemas/Tag")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function update(Request $request, $postId, $tagId)
    {
        $validator = Validator::make($request->all(), self::validationArray());

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->messages()
            ], 400);
        }

        $post = Post::where('user_id', Auth::user()->id)->where('id', $postId)->first();

        $postTag = $post->tags()->where('tags.id', $tagId)->first();

        $postTag->update([
            'user_id'           => Auth::user()->id,
            'name'              => $request->name,
            'description'       => $request->description,
            'rbg_color_code'    => $request->rbgColorCode,
            'created_by'        => $request->createdBy,
        ]);

        return (new TagResource($postTag))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Delete(
     *     path="user/posts/{postId}/tags/{tagId}",
     *     description="Delete a single Tag based on the  postId",
     *     operationId="deleteTag based on the  postId",
     *     summary="Delete a Tag based on the  postId",
     *     tags={"TagsPost"},
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
     *         description="tagId",
     *         in="path",
     *         name="tagId",
     *         required=true,
     *         @OA\Schema(
     *             format="int64",
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tag response",
     *         @OA\JsonContent(ref="#/components/schemas/Tag")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function destroy($postId, $tagId)
    {
        $post = Post::where('user_id', Auth::user()->id)->where('id', $postId)->first();

        $postTag = $post->tags()->where('tags.id', $tagId)->first();

        Taggable::where('tag_id', $tagId)->where('taggable_id', $postId)
            ->where('taggable_type', 'App\Post')->delete();

        $postTag->delete();

        return (new TagResource($postTag))
            ->response()
            ->setStatusCode(200);
    }

    /*
    * Validation rules for saving to table
    */
    public static function validationArray():array {
        $validation = array (
            "name"         => 'required|string|max:255|unique:tags,name,user_id'.  Auth::user()->id,
            "description"  => 'required|string|max:255',
            "rbgColorCode" => 'required|string|max:255',
        );

        return $validation;
    }

    /*
     * Validation messages
     */
    public static function validationMessages():array {
        $validationMessages = array (
            'name.required' => 'This name field is required',
        );
        return $validationMessages;
    }
}
