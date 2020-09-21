<?php

namespace App\Http\Controllers;

use App\Http\Helpers;
use App\Http\Resources\ProfileCollection;
use App\Http\Resources\ProfileResource;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Project;
use App\Team;
use App\Teamable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class TeamProjectController extends Controller
{
    /**
     * @OA\Get(
     *     path="user/projects/{projectId}/teams",
     *     summary="Get a list of teams based on projectId",
     *     tags={"TeamsProject"},
     *     description="Returns all teams from the system that the user has access to.",
     *     operationId="findTeamsProject",
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
    public function index(Request $request, $projectId)
    {
        $project = Project::where('id', $projectId)->first();

        $projectTeams =  $project->teams()->get();

        return (new ProfileCollection($projectTeams))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Post(
     *     path="user/projects/{projectId}/teams",
     *     operationId="addTeam to Project",
     *     description="Creates a new Team for Project",
     *     summary="Create a new Team for a Project",
     *     tags={"TeamsProject"},
     *     @OA\Response(
     *         response=200,
     *         description="Project response",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function store(Request $request, $projectId)
    {
        $validator = Validator::make($request->all(),  self::validationArray());

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->messages()
            ], 400);
        }

        $project = Project::find($projectId);

        $array = [];
        if(!empty($request->userId) && $request->userId){
            foreach ($request->userId as $item){
                array_push($array, $item['id']);
            }
        }

        $project->teams()->attach($array);

        $projectTeam = $project->teams()
            ->where('teamables.teamable_id', $project->id)
            ->first();

        return (new ProfileResource($projectTeam))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="user/projects/{projectId}/teams/{userId}",
     *     summary="Get a single Team based on the  projectId and userId",
     *     tags={"TeamsProject"},
     *     description="Returns a single Team",
     *     operationId="showTeam",
     *     @OA\Parameter(
     *         name="projectId",
     *         in="path",
     *         description="id of the project",
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
    public function show($projectId, $userId)
    {
        $project = Project::where('id', $projectId)->first();

        $projectTeam = $project->teams()
            ->where('teamables.user_id', $userId)
            ->where('teamables.teamable_id', $project->id)
            ->first();

        return (new ProfileResource($projectTeam))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Delete(
     *     path="user/projects/{projectId}/teams/{userId}",
     *     description="Delete a single Team based on the  projectId",
     *     operationId="deleteTeam based on the  projectId",
     *     summary="Delete a Team based on the  projectId",
     *     tags={"TeamsProject"},
     *     @OA\Parameter(
     *         description="projectId",
     *         in="path",
     *         name="projectId",
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
    public function destroy($projectId, $userId)
    {
        $project = Project::where('id', $projectId)->first();
        $projectTeam = $project->teams()
            ->where('teamables.user_id', $userId)
            ->where('teamables.teamable_id', $project->id)
            ->first();
        Teamable::where('teamables.user_id', $userId)
            ->where('teamables.teamable_id', $project->id)
            ->where('teamable_type', 'App\Project')->delete();

        return (new ProfileResource($projectTeam))
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
            'userId.required' => 'This user id  field is required',
        );

        return $validationMessages;
    }
}
