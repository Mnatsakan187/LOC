<?php

namespace App\Http\Controllers;

use App\Http\Helpers;
use App\Http\Resources\LikeCollection;
use App\Http\Resources\LikeResource;
use App\Event;
use App\Like;
use App\Likeable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class LikeEventController extends Controller
{
    /**
     * @OA\Get(
     *     path="user/events/{eventId}/likes",
     *     summary="Get a list of likes based on eventId",
     *     tags={"LikesEvent"},
     *     description="Returns all likes from the system that the user has access to.",
     *     operationId="findLikesEvent",
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
    public function index(Request $request, $eventId)
    {
        $event = Event::where('id', $eventId)->first();

        $eventLikes =  $event->likes()->paginate(10);

        return (new LikeCollection($eventLikes))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Post(
     *     path="user/events/{eventId}/likes",
     *     operationId="addLike to Event",
     *     description="Creates a new Like for Event",
     *     summary="Create a new Like for a Event",
     *     tags={"LikesEvent"},
     *     @OA\Response(
     *         response=200,
     *         description="Event response",
     *         @OA\JsonContent(ref="#/components/schemas/Like")
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
        $event = Event::find($eventId);

        $like = Like::create([
            'user_id'           => Auth::user()->id,
            'liked_date'        => Carbon::now()->toDateTimeString(),
        ]);

        $event->likes()->attach([$like->id]);

        $eventLike = $event->likes()->where('likes.id', $like->id)->first();

        $notificationId = Helpers::storeNotification($event->user_id, 'like', 'event', $eventId, null, $event->name);

        event(new \App\Events\NewNotification($notificationId, $event->user_id));

        return (new LikeResource($eventLike))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="user/events/{eventId}/likes/{likeId}",
     *     summary="Get a single Like based on the  eventId and likeId",
     *     tags={"LikesEvent"},
     *     description="Returns a single Like",
     *     operationId="showLike",
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
    public function show($eventId, $likeId)
    {
        $event = Event::where('user_id', Auth::user()->id)->where('id', $eventId)->first();

        $eventLike = $event->likes()->where('likes.id', $likeId)->first();

        return (new LikeResource($eventLike))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Delete(
     *     path="user/events/{eventId}/likes/{likeId}",
     *     description="Delete a single Like based on the  eventId",
     *     operationId="deleteLike based on the  eventId",
     *     summary="Delete a Like based on the  eventId",
     *     tags={"LikesEvent"},
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
    public function destroy($eventId, $likeId)
    {
        $event = Event::where('id', $eventId)->first();

        $eventLike = $event->likes()->where('likes.id', $likeId)->first();

        Likeable::where('like_id', $likeId)->where('likeable_id', $eventId)
            ->where('likeable_type', 'App\Event')->delete();

        $eventLike->delete();

        return (new LikeResource($eventLike))
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
