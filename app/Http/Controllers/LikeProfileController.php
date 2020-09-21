<?php

namespace App\Http\Controllers;

use App\Http\Helpers;
use App\Http\Resources\LikeCollection;
use App\Http\Resources\LikeResource;
use App\Profile;
use App\Like;
use App\Likeable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class LikeProfileController extends Controller
{
    /**
     * @OA\Get(
     *     path="user/profiles/{profileId}/likes",
     *     summary="Get a list of likes based on profileId",
     *     tags={"LikesProfile"},
     *     description="Returns all likes from the system that the user has access to.",
     *     operationId="findLikesProfile",
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
    public function index(Request $request, $profileId)
    {
        $profile = Profile::where('id', $profileId)->first();

        $profileLikes =  $profile->likes()->paginate(10);

        return (new LikeCollection($profileLikes))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Post(
     *     path="user/profiles/{profileId}/likes",
     *     operationId="addLike to Profile",
     *     description="Creates a new Like for Profile",
     *     summary="Create a new Like for a Profile",
     *     tags={"LikesProfile"},
     *     @OA\Response(
     *         response=200,
     *         description="Profile response",
     *         @OA\JsonContent(ref="#/components/schemas/Like")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function store(Request $request, $profileId)
    {
        $profile = Profile::find($profileId);

        $like = Like::create([
            'user_id'           => Auth::user()->id,
            'liked_date'        => Carbon::now()->toDateTimeString()
        ]);

        $profile->likes()->attach([$like->id]);

        $profileLike = $profile->likes()->where('likes.id', $like->id)->first();

        if($profile->user_id != Auth::user()->id){

            $notificationId = Helpers::storeNotification($profile->user_id, 'like', 'profile', $profileId, null, $profile->creative_title);

            event(new \App\Events\NewNotification($notificationId, $profile->user_id));
        }

        return (new LikeResource($profileLike))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="user/profiles/{profileId}/likes/{likeId}",
     *     summary="Get a single Like based on the  profileId and likeId",
     *     tags={"LikesProfile"},
     *     description="Returns a single Like",
     *     operationId="showLike",
     *     @OA\Parameter(
     *         name="profileId",
     *         in="path",
     *         description="id of the profile",
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
    public function show($profileId, $likeId)
    {
        $profile = Profile::where('user_id', Auth::user()->id)->where('id', $profileId)->first();

        $profileLike = $profile->likes()->where('likes.id', $likeId)->first();

        return (new LikeResource($profileLike))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Delete(
     *     path="user/profiles/{profileId}/likes/{likeId}",
     *     description="Delete a single Like based on the  profileId",
     *     operationId="deleteLike based on the  profileId",
     *     summary="Delete a Like based on the  profileId",
     *     tags={"LikesProfile"},
     *     @OA\Parameter(
     *         description="profileId",
     *         in="path",
     *         name="profileId",
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
    public function destroy($profileId, $likeId)
    {
        $profile = Profile::where('id', $profileId)->first();
        $profileLike = $profile->likes()->where('likes.id', $likeId)->first();

        Likeable::where('like_id', $likeId)->where('likeable_id', $profileId)
            ->where('likeable_type', 'App\Profile')->delete();

        $profileLike->delete();

        return (new LikeResource($profileLike))
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
