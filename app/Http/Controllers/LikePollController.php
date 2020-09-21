<?php

namespace App\Http\Controllers;

use App\Http\Helpers;
use App\Http\Resources\PollCollection;
use App\Http\Resources\LikeResource;

use App\Like;
use App\Likeable;
use App\Poll;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class LikePollController extends Controller
{
    /**
     * @OA\Get(
     *     path="user/polls/{pollId}/likes",
     *     summary="Get a list of likes based on pollId",
     *     tags={"LikesPoll"},
     *     description="Returns all likes from the system that the user has access to.",
     *     operationId="findLikesPoll",
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
    public function index(Request $request, $pollId)
    {
        $poll = Poll::where('id', $pollId)->first();

        $pollLikes =  $poll->likes()->paginate(10);

        return (new PollCollection($pollLikes))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Post(
     *     path="user/polls/{pollId}/likes",
     *     operationId="addLike to Poll",
     *     description="Creates a new Like for Poll",
     *     summary="Create a new Like for a Poll",
     *     tags={"LikesPoll"},
     *     @OA\Response(
     *         response=200,
     *         description="Poll response",
     *         @OA\JsonContent(ref="#/components/schemas/Like")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function store(Request $request, $pollId)
    {
        $poll = Poll::find($pollId);

        $like = Like::create([
            'user_id'           => Auth::user()->id,
            'liked_date'        => Carbon::now()->toDateTimeString(),
        ]);

        $poll->likes()->attach([$like->id]);

        $pollLike = $poll->likes()->where('likes.id', $like->id)->first();

        if($poll->user_id != Auth::user()->id){

            $notificationId = Helpers::storeNotification($poll->user_id, 'like', 'poll', $pollId, null, $poll->name);

            event(new \App\Events\NewNotification($notificationId, $poll->user_id));
        }



        return (new LikeResource($pollLike))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="user/polls/{pollId}/likes/{likeId}",
     *     summary="Get a single Like based on the  pollId and likeId",
     *     tags={"LikesPoll"},
     *     description="Returns a single Like",
     *     operationId="showLike",
     *     @OA\Parameter(
     *         name="pollId",
     *         in="path",
     *         description="id of the Poll",
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
    public function show($pollId, $likeId)
    {
        $poll = Poll::where('user_id', Auth::user()->id)->where('id', $pollId)->first();

        $pollLike = $poll->likes()->where('likes.id', $likeId)->first();

        return (new LikeResource($pollLike))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Delete(
     *     path="user/polls/{pollId}/likes/{likeId}",
     *     description="Delete a single Like based on the  pollId",
     *     operationId="deleteLike based on the  pollId",
     *     summary="Delete a Like based on the  pollId",
     *     tags={"LikesPoll"},
     *     @OA\Parameter(
     *         description="pollId",
     *         in="path",
     *         name="pollId",
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
    public function destroy($pollId, $likeId)
    {
        $poll = Poll::where('id', $pollId)->first();

        $pollLike = $poll->likes()->where('likes.id', $likeId)->first();

        Likeable::where('like_id', $likeId)->where('likeable_id', $pollId)
            ->where('likeable_type', 'App\Poll')->delete();

        $pollLike->delete();

        return (new LikeResource($pollLike))
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
