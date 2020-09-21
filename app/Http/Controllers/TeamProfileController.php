<?php

namespace App\Http\Controllers;

use App\Http\Helpers;
use App\Http\Resources\ProfileCollection;
use App\Http\Resources\ProfileResource;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Profile;
use App\Team;
use App\Teamable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class TeamProfileController extends Controller
{
    /**
     * @OA\Get(
     *     path="user/profiles/{profileId}/teams",
     *     summary="Get a list of teams based on profileId",
     *     tags={"TeamsProfile"},
     *     description="Returns all teams from the system that the user has access to.",
     *     operationId="findTeamsProfile",
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
     *         description="Team response",
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
    public function index(Request $request, $profileId)
    {
        $profile = Profile::where('id', $profileId)->first();

        $profileTeams =  $profile->teams()->paginate(10);

        return (new ProfileCollection($profileTeams))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Post(
     *     path="user/profiles/{profileId}/teams",
     *     operationId="addTeam to Profile",
     *     description="Creates a new Team for Profile",
     *     summary="Create a new Team for a Profile",
     *     tags={"TeamsProfile"},
     *     @OA\Response(
     *         response=200,
     *         description="Profile response",
     *         @OA\JsonContent(ref="#/components/schemas/User")
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

        $validator = Validator::make($request->all(),  self::validationArray());

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->messages()
            ], 400);
        }

        $profile = Profile::find($profileId);

        $array = [];

        if(!empty($request->userId) && $request->userId){
            foreach ($request->userId as $item){
                array_push($array, $item['id']);
            }
        }

        $profile->teams()->attach($array);

        $profileTeam = $profile->teams()
            ->where('teamables.teamable_id', $profile->id)
            ->first();

        return (new ProfileResource($profileTeam))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="user/profiles/{profileId}/teams/{userId}",
     *     summary="Get a single Team based on the  profileId and userId",
     *     tags={"TeamsProfile"},
     *     description="Returns a single Team",
     *     operationId="showTeam",
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
     *         name="userId",
     *         in="path",
     *         description="id of the team",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Team response",
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
    public function show($profileId, $userId)
    {
        $profile = Profile::where('id', $profileId)->first();

        $profileTeam = $profile->teams()
            ->where('teamables.user_id', $userId)
            ->where('teamables.teamable_id', $profile->id)
            ->first();

        return (new ProfileResource($profileTeam))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Delete(
     *     path="user/profiles/{profileId}/teams/{userId}",
     *     description="Delete a single Team based on the  profileId",
     *     operationId="deleteTeam based on the  profileId",
     *     summary="Delete a Team based on the  profileId",
     *     tags={"TeamsProfile"},
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
     *         description="userId",
     *         in="path",
     *         name="userId",
     *         required=true,
     *         @OA\Schema(
     *             format="int64",
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Team response",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function destroy($profileId, $userId)
    {
        $profile = Profile::where('id', $profileId)->first();
        $profileTeam = $profile->teams()
            ->where('teamables.user_id', $userId)
            ->where('teamables.teamable_id', $profile->id)
            ->first();
        Teamable::where('teamables.user_id', $userId)
            ->where('teamables.teamable_id', $profile->id)
            ->where('teamable_type', 'App\Profile')->delete();

        return (new ProfileResource($profileTeam))
            ->response()
            ->setStatusCode(200);
    }

    /*
    * Validation rules for saving to table
    */
    public static function validationArray():array {
        $validation = array (
            "userId"         => 'required',
        );

        return $validation;
    }

    /*
     * Validation messages
     */
    public static function validationMessages():array {
        $validationMessages = array (
            'userId.required' => 'This user id field is required',
        );

        return $validationMessages;
    }
}
