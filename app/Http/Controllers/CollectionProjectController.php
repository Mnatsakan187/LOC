<?php

namespace App\Http\Controllers;

use App\Collection;
use App\Http\Resources\CollectionCollection;
use App\Http\Resources\CollectionResource;
use App\Http\Resources\ProjectCollection;
use App\Http\Resources\ProjectResource;
use App\Project;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class CollectionProjectController extends Controller
{
    /**
     * @OA\Get(
     *     path="/user/collections/{collectionId}/projects",
     *     summary="Get a list of collection  of projects",
     *     tags={"CollectionProjects"},
     *     description="Returns all projects from the collections",
     *     operationId="findProjects",
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
     *             @OA\Items(ref="#/components/schemas/Project")
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

        $projects = $collection->projects()->paginate(10);

        return ( new ProjectCollection($projects))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Post(
     *     path="user/collections/{collectionId}/projects/{projectId}",
     *     operationId="add",
     *     description="Add project to collections",
     *     summary="Add project to collections",
     *     tags={"CollectionProjects"},
     *     @OA\Response(
     *         response=200,
     *         description="Collection response",
     *         @OA\JsonContent(ref="#/components/schemas/Project")
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
    public function store(Request $request, $collectionId, $projectId)
    {
        $collection = Collection::findorFail($collectionId);

        $collection->projects()->attach($projectId);

        Collection::where('id', $collection->id)->update(['updated' => 1,  'updated_at' => Carbon::now()->toDateTimeString()]);

        $project = $collection->projects()->where('projects.id', $projectId)->first();

        return (new ProjectResource($project))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="/user/collections/{collectionId}/projects/{projectId}",
     *     summary="Get a single project from collection",
     *     tags={"CollectionProjects"},
     *     description="Returns a single project from collection",
     *     operationId="showProject",
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
     *         name="projectId",
     *         in="path",
     *         description="id of the project",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Project response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Project")
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
    public function show($collectionId, $projectId)
    {
        $collection = Collection::where('user_id', Auth::user()->id)->where('id', $collectionId)->first();
        $project = $collection->projects()->where('projects.id', $projectId)->first();

        return ( new ProjectResource($project))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Delete(
     *     path="/user/collections/{collectionId}/projects/{projectId}",
     *     description="Delete a single project  from collection",
     *     operationId="deleteProject",
     *     summary="Delete a project from collection",
     *     tags={"CollectionProjects"},
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
     *         description="projectId",
     *         in="path",
     *         name="projectId",
     *         required=true,
     *         @OA\Schema(
     *             format="int64",
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Project response",
     *         @OA\JsonContent(ref="#/components/schemas/Project")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function destroy($collectionId, $projectId)
    {
        $collection = Collection::findorFail($collectionId);

        $project = $collection->projects()->where('projects.id', $projectId)->first();

        $collection->projects()->detach($projectId);

        return ( new ProjectResource($project))
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
            'projectId.required' => 'This  project already belong to this collection',
        );
        return $validationMessages;
    }
}
