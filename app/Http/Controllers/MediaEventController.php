<?php

namespace App\Http\Controllers;

use App\Http\Resources\MediaCollection;
use App\Http\Resources\MediaResource;
use App\Event;
use App\Media;
use App\Mediable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class MediaEventController extends Controller
{
    /**
     * @OA\Get(
     *     path="user/events/{eventId}/media",
     *     summary="Get a list of media based on eventId",
     *     tags={"MediumEvent"},
     *     description="Returns all media from the system that the user has access to.",
     *     operationId="findMediaEvent",
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="filter by Media name text",
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
     *         description="Media response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Media")
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

        $eventMedium =  $event->media()->paginate(10);

        // Search for summary text
        if ($request->has('search')) {
            $eventMedium->where('name', $request->input('search'));
        }

        return (new MediaCollection($eventMedium))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Post(
     *     path="user/events/{eventId}/media",
     *     operationId="addMedia to Event",
     *     description="Creates a new Media for Event",
     *     summary="Create a new Media for a Event",
     *     tags={"MediumEvent"},
     *     @OA\Response(
     *         response=200,
     *         description="Media response",
     *         @OA\JsonContent(ref="#/components/schemas/Media")
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

        $media = Media::create([
            'user_id'           => Auth::user()->id,
            'display_name'      => $request->displayName,
            'field_name'        => $request->fieldName,
            'uri'               => $request->uri,
            'created_by'        => $request->createdBy,
            'updated_by'        => $request->updatedBy,
            'media_type'        => $request->mediaType,
        ]);

        $event->media()->attach([$media->id]);

        $eventMedia = $event->media()->where('media.id', $media->id)->first();

        return (new MediaResource($eventMedia))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="user/events/{eventId}/media/{mediaId}",
     *     summary="Get a single Media based on the  eventId and tagId",
     *     tags={"MediumEvent"},
     *     description="Returns a single Media",
     *     operationId="showMedia",
     *     @OA\Parameter(
     *         name="eventId",
     *         in="path",
     *         description="id of the event",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="mediaId",
     *         in="path",
     *         description="id of the media",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Media response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Media")
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
    public function show($eventId, $mediaId)
    {
        $event = Event::where('user_id', Auth::user()->id)->where('id', $eventId)->first();

        $eventMedia = $event->media()->where('media.id', $mediaId)->first();

        return (new MediaResource($eventMedia))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Put(
     *     path="user/events/{eventId}/media/{mediaId}",
     *     operationId="edit Media based on the Event",
     *     description="Edits a Media based on the Event",
     *     summary="Edit a Media based on the Event",
     *     tags={"MediumEvent"},
     *     @OA\Parameter(
     *         name="eventId",
     *         in="path",
     *         description="id of the event",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="mediaId",
     *         in="path",
     *         description="id of the media",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Media to edit",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/Media")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Media response",
     *         @OA\JsonContent(ref="#/components/schemas/Media")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function update(Request $request, $eventId, $mediaId)
    {
        $validator = Validator::make($request->all(), self::validationArray());

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->messages()
            ], 400);
        }

        $event = Event::where('user_id', Auth::user()->id)->where('id', $eventId)->first();

        $eventMedia = $event->media()->where('media.id', $mediaId)->first();

        $eventMedia->update([
            'user_id'           => Auth::user()->id,
            'display_name'      => $request->displayName,
            'field_name'        => $request->fieldName,
            'uri'               => $request->uri,
            'created_by'        => $request->createdBy,
            'updated_by'        => $request->updatedBy,
            'media_type'        => $request->mediaType,
        ]);

        return (new MediaResource($eventMedia))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Delete(
     *     path="user/events/{eventId}/media/{mediaId}",
     *     description="Delete a single Media based on the  eventId",
     *     operationId="deleteMedia based on the  eventId",
     *     summary="Delete a Media based on the  eventId",
     *     tags={"MediumEvent"},
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
     *         description="mediaId",
     *         in="path",
     *         name="mediaId",
     *         required=true,
     *         @OA\Schema(
     *             format="int64",
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Media response",
     *         @OA\JsonContent(ref="#/components/schemas/Media")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function destroy($eventId, $mediaId)
    {
        $event = Event::where('user_id', Auth::user()->id)->where('id', $eventId)->first();

        $eventMedia = $event->media()->where('media.id', $mediaId)->first();

        Mediable::where('media_id', $mediaId)->where('mediable_id', $eventId)
            ->where('mediable_type', 'App\Event')->delete();
        $eventMedia->delete();

        return (new MediaResource($eventMedia))
            ->response()
            ->setStatusCode(200);
    }

    /*
    * Validation rules for saving to table
    */
    public static function validationArray():array {
        $validation = array (
            "displayName"   => 'required|string|max:255',
            "fieldName"     => 'required|string|max:255',
            "uri"           => 'required|string|max:255',
            "mediaType"     => 'required|integer|max:255',
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
