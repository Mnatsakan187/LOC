<?php

namespace App\Http\Controllers;

use App\Event;
use App\Http\Resources\TagCollection;
use App\Http\Resources\TagResource;
use App\Tag;
use App\Taggable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class TagEventController extends Controller
{
    /**
     * @OA\Get(
     *     path="user/events/{eventId}/tags",
     *     summary="Get a list of tags based on eventId",
     *     tags={"TagsEvent"},
     *     description="Returns all tags from the system that the user has access to.",
     *     operationId="findTagsEvent",
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
    public function index(Request $request, $eventId)
    {
        $event = Event::where('user_id', Auth::user()->id)->where('id', $eventId)->first();

        $eventTags =  $event->tags()->paginate(10);

        // Search for summary text
        if ($request->has('search')) {
            $eventTags->where('name', $request->input('search'));
        }

        return (new TagCollection($eventTags))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Post(
     *     path="user/events/{eventId}/tags",
     *     operationId="addTag to Event",
     *     description="Creates a new Tag for Event",
     *     summary="Create a new Tag for a Event",
     *     tags={"TagsEvent"},
     *     @OA\Response(
     *         response=200,
     *         description="Event response",
     *         @OA\JsonContent(ref="#/components/schemas/Tag")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function store(Request $request, $eventId)
    {
        $validator = Validator::make($request->all(),  self::validationArray());

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->messages()
            ], 400);
        }

        $event = Event::find($eventId);

        $tag = Tag::create([
            'user_id'           => Auth::user()->id,
            'name'              => $request->name,
            'description'       => $request->description,
            'rbg_color_code'    => $request->rbgColorCode,
            'created_by'        => $request->createdBy,
        ]);

        $event->tags()->attach([$tag->id]);

        $eventTags = $event->tags()->first();

        return (new TagResource($eventTags))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="user/events/{eventId}/tags/{tagId}",
     *     summary="Get a single Tag based on the  eventId and tagId",
     *     tags={"TagsEvent"},
     *     description="Returns a single Tag",
     *     operationId="showTag",
     *     @OA\Parameter(
     *         name="eventId",
     *         in="path",
     *         description="id of the Event",
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
    public function show($eventId, $tagId)
    {
        $event = Event::where('user_id', Auth::user()->id)->where('id', $eventId)->first();

        $eventTag = $event->tags()->where('tags.id', $tagId)->first();

        return (new TagResource($eventTag))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Put(
     *     path="user/events/{eventId}/tags/{tagId}",
     *     operationId="edit Tag based on the Event",
     *     description="Edits a Tag based on the Event",
     *     summary="Edit a Tag based on the Event",
     *     tags={"TagsEvent"},
     *     @OA\Parameter(
     *         name="eventId",
     *         in="path",
     *         description="id of the Event",
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
    public function update(Request $request, $eventId, $tagId)
    {
        $validator = Validator::make($request->all(), self::validationArray());

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->messages()
            ], 400);
        }

        $event = Event::where('user_id', Auth::user()->id)->where('id', $eventId)->first();

        $postTag = $event->tags()->where('tags.id', $tagId)->first();

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
     *     path="user/events/{eventId}/tags/{tagId}",
     *     description="Delete a single Tag based on the  eventId",
     *     operationId="deleteTag based on the  eventId",
     *     summary="Delete a Tag based on the  eventId",
     *     tags={"TagsEvent"},
     *     @OA\Parameter(
     *         description="eventId",
     *         in="path",
     *         name="eventId",
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
    public function destroy($eventId, $tagId)
    {
        $event = Event::where('user_id', Auth::user()->id)->where('id', $eventId)->first();

        $postTag = $event->tags()->where('tags.id', $tagId)->first();

        Taggable::where('tag_id', $tagId)->where('taggable_id', $eventId)
            ->where('taggable_type', 'App\Event')->delete();

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
