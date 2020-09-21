<?php

namespace App\Http\Controllers;

use App\Http\Helpers;
use App\Http\Resources\LikeCollection;
use App\Http\Resources\LikeResource;
use App\Project;
use App\Like;
use App\Likeable;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class LikeProjectController extends Controller
{
    /**
     * @OA\Get(
     *     path="user/projects/{projectId}/likes",
     *     summary="Get a list of likes based on projectId",
     *     tags={"LikesProject"},
     *     description="Returns all likes from the system that the user has access to.",
     *     operationId="findLikesProject",
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
    public function index(Request $request, $projectId)
    {
        $project = Project::where('id', $projectId)->first();

        $projectLikes =  $project->likes()->paginate(10);

        return (new LikeCollection($projectLikes))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Post(
     *     path="user/projects/{projectId}/likes",
     *     operationId="addLike to Project",
     *     description="Creates a new Like for Project",
     *     summary="Create a new Like for a Project",
     *     tags={"LikesProject"},
     *     @OA\Response(
     *         response=200,
     *         description="Project response",
     *         @OA\JsonContent(ref="#/components/schemas/Like")
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
        /** @var Project $project */
        $project = Project::find($projectId);

        /** @var User $user */
        $user = Auth::user();

        $like = $user->likes()->create([
            'liked_date'        => Carbon::now()->toDateTimeString()
        ]);

        $project->likes()->attach($like->getKey());

        $projectLike = $project->likes()->where('likes.id', $like->id)->first();

        Project::where('id', $projectId)->update(['updated' => 1,  'updated_at' => Carbon::now()->toDateTimeString()]);

        if(!$project->user->is($user)){

            $notificationId = Helpers::storeNotification($project->user_id, 'like', 'project', $projectId, null, $project->name);

            event(new \App\Events\NewNotification($notificationId, $project->user->getKey()));
        }

        return (new LikeResource($projectLike))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="user/projects/{projectId}/likes/{likeId}",
     *     summary="Get a single Like based on the  projectId and likeId",
     *     tags={"LikesProject"},
     *     description="Returns a single Like",
     *     operationId="showLike",
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
    public function show($projectId, $likeId)
    {
        $project = Project::where('user_id', Auth::user()->id)->where('id', $projectId)->first();

        $projectLike = $project->likes()->where('likes.id', $likeId)->first();

        return (new LikeResource($projectLike))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Delete(
     *     path="user/projects/{projectId}/likes/{likeId}",
     *     description="Delete a single Like based on the  projectId",
     *     operationId="deleteLike based on the  projectId",
     *     summary="Delete a Like based on the  projectId",
     *     tags={"LikesProject"},
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
    public function destroy($projectId, $likeId)
    {
        $project = Project::where('id', $projectId)->first();
        $projectLike = $project->likes()->where('likes.id', $likeId)->first();

        Likeable::where('like_id', $likeId)->where('likeable_id', $projectId)
            ->where('likeable_type', 'App\Project')->delete();

        Project::where('id', $projectId)->update(['updated' => 0,  'updated_at' => Carbon::now()->toDateTimeString()]);

        $projectLike->delete();

        return (new LikeResource($projectLike))
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
