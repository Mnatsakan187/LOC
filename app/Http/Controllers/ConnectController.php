<?php

namespace App\Http\Controllers;

use App\Connect;
use App\FollowUser;
use App\Http\Helpers;
use App\Http\Resources\ProfileCollection;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Notification;
use App\Profile;
use App\SocialMediaLink;
use App\User;
use Carbon\Carbon;
use function GuzzleHttp\Promise\all;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Claims\Audience;
use Validator;
use App\Follow;
use Illuminate\Support\Facades\Storage;
use Analytics;
use Spatie\Analytics\Period;

class ConnectController extends Controller
{
    /**
     * @OA\Get(
     *     path="/user/connection",
     *     summary="Get a list of follows",
     *     tags={"Follows"},
     *     description="Returns all follow from the system that the user has access to.",
     *     operationId="findFollow",
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
     *         description="Follow response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/User")
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
    public function index()
    {
        $user = User::where('id', Auth::user()->id)->first();
        $connection = $user->connection()->get();
        return (new UserCollection($connection))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Post(
     *     path="/user/follows/{followId}",
     *     operationId="addFollow",
     *     description="Creates a new follow for user",
     *     summary="Create a new follow for a user",
     *     tags={"Follows"},
     *     @OA\Response(
     *         response=200,
     *         description="Follow response",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function store(Request $request, $profile)
    {

        $validator = Validator::make(['id' => $profile],  self::validationArray());

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->messages()
            ], 400);
        }

        $connect = Connect::create([
            'user_id'            => Auth::user()->id,
            'profile_id'  => $profile,
            'date'        => Carbon::now()->toDateTimeString(),
        ]);

        $user = User::findOrFail($connect->user_id);

        $profile = Profile::find($connect->profile_id);
        $notificationId = Helpers::storeNotification($profile->user_id, 'connect', 'user', $user->id, $profile->id, $profile->creative_title);

        event(new \App\Events\NewNotification($notificationId, $profile->user_id));

        return (new UserResource($user))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="/user/follows/{followId}",
     *     summary="Get a single follow",
     *     tags={"Follows"},
     *     description="Returns a single follow",
     *     operationId="showFollow",
     *     @OA\Parameter(
     *         name="followId",
     *         in="path",
     *         description="id of the follow",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Follow response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/User")
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

    public function show($profile)
    {
        $follow = User::where('id', $profile)->first();
        return (new UserResource($follow))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Delete(
     *     path="/user/connection/{profileId}",
     *     description="Delete a single connect based on the  connectId",
     *     operationId="deleteConnect",
     *     summary="Delete a connect",
     *     tags={"Connects"},
     *     @OA\Parameter(
     *         description="connectId",
     *         in="path",
     *         name="connectId",
     *         required=true,
     *         @OA\Schema(
     *             format="int64",
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Connect response",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function destroy($profileId)
    {
        $connect = Connect::where('profile_id', $profileId)
            ->where('user_id', Auth::user()->id)
            ->first();

        $user = User::where('id', $connect->user_id)->first();

        $connect->delete();

        return (new UserResource($user))
            ->response()
            ->setStatusCode(200);
    }

    public function accept($notificationId){
        $notification = Notification::where('id', $notificationId)->first();

        Connect::where('profile_id', $notification->profile_id)
            ->where('user_id', $notification->created_by)->update(['accept' => 1]);

        $connect = Connect::where('profile_id', $notification->profile_id)
            ->where('user_id', $notification->created_by)->first();

        $profile = Profile::find($connect->profile_id);
        $user = User::where('id', $connect->user_id)->first();

        $notificationId = Helpers::storeNotification($notification->created_by, 'accept', 'user', $user->id, $profile->id, $profile->creative_title);

        event(new \App\Events\NewNotification($notificationId, $notification->created_by));

        $notification->delete();
        return (new UserResource($user))
            ->response()
            ->setStatusCode(200);

    }


    public function connectionProfile()
    {
        $user = Auth::user();

        $connectedProfile = $user->connectionProfile()->get();

        return (new ProfileCollection($connectedProfile))
            ->response()
            ->setStatusCode(200);
    }

    /*
    * Validation rules for saving to table
    */
    public static function validationArray():array {
        $validation = array (
            'id' => 'required|numeric',
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
