<?php

namespace App\Http\Controllers;

use App\Collection;
use App\Http\Resources\CollectionCollection;
use App\Http\Resources\CollectionResource;
use App\Http\Resources\EventCollection;
use App\Http\Resources\EventResource;
use App\Event;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class CollectionEventController extends Controller
{
    /**
     * @OA\Get(
     *     path="/user/collections/{collectionId}/events",
     *     summary="Get a list of collection  of events",
     *     tags={"CollectionEvents"},
     *     description="Returns all events from the collections",
     *     operationId="findEvents",
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
     *     @OA\Parameter(
     *         name="collectionId",
     *         in="path",
     *         description="id of the collectionId",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Collection response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Event")
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
    public function index(Request $request, $collectionId)
    {
        $collection = Collection::where('user_id', Auth::user()->id)
            ->where('id', $collectionId)->first();

        $events = $collection->events()->paginate(10);

        return ( new EventCollection($events))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Post(
     *     path="user/collections/{collectionId}/events/{eventId}",
     *     operationId="add",
     *     description="Add event to collections",
     *     summary="Add event to collections",
     *     tags={"CollectionEvents"},
     *     @OA\Response(
     *         response=200,
     *         description="Collection response",
     *         @OA\JsonContent(ref="#/components/schemas/Event")
     *     ),
     *     @OA\Parameter(
     *         name="collectionId",
     *         in="path",
     *         description="id of the collectionId",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function store(Request $request, $collectionId, $eventId)
    {
        $collection = Collection::findorFail($collectionId);

        $collection->events()->attach($eventId);

        Collection::where('id', $collection->id)->update(['updated' => 1,  'updated_at' => Carbon::now()->toDateTimeString()]);
        $event = $collection->events()->where('events.id', $eventId)->first();

        return (new EventResource($event))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="/user/collections/{collectionId}/events/{eventId}",
     *     summary="Get a single event from collection",
     *     tags={"CollectionEvents"},
     *     description="Returns a single event from collection",
     *     operationId="showEvent",
     *     @OA\Parameter(
     *         name="collectionId",
     *         in="path",
     *         description="id of the collection",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
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
     *     @OA\Response(
     *         response=200,
     *         description="Event response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Event")
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
    public function show($collectionId, $eventId)
    {
        $collection = Collection::where('user_id', Auth::user()->id)->where('id', $collectionId)->first();
        $event = $collection->events()->where('events.id', $eventId)->first();

        return ( new EventResource($event))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Delete(
     *     path="/user/collections/{collectionId}/events/{eventId}",
     *     description="Delete a single event  from collection",
     *     operationId="deleteEvent",
     *     summary="Delete a event from collection",
     *     tags={"CollectionEvents"},
     *     @OA\Parameter(
     *         description="collectionId",
     *         in="path",
     *         name="collectionId",
     *         required=true,
     *         @OA\Schema(
     *             format="int64",
     *             type="integer"
     *         )
     *     ),
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
     *     @OA\Response(
     *         response=200,
     *         description="Event response",
     *         @OA\JsonContent(ref="#/components/schemas/Event")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function destroy($collectionId, $eventId)
    {
        $collection = Collection::findorFail($collectionId);

        $event = $collection->events()->where('events.id', $eventId)->first();

        $collection->events()->detach($eventId);

        return ( new EventResource($event))
            ->response()
            ->setStatusCode(200);
    }

    /*
     * Validation rules for saving to table
     */
    public static function validationArray():array {
        $validation = array (
            "name" => 'required|string|max:255',
        );

        return $validation;
    }

    /*
     * Validation messages
     */
    public static function validationMessages():array {
        $validationMessages = array (
            'eventId.required' => 'This  event already belong to this collection',
        );
        return $validationMessages;
    }
}
