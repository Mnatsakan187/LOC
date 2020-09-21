<?php

namespace App\Http\Controllers;

use App\Http\Resources\TagCollection;
use App\Http\Resources\TagResource;
use App\Project;
use App\Tag;
use App\Taggable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class TagProjectController extends Controller
{
    /**
     * @OA\Get(
     *     path="user/projects/{projectId}/tags",
     *     summary="Get a list of tags based on projectId",
     *     tags={"TagsProject"},
     *     description="Returns all tags from the system that the user has access to.",
     *     operationId="findTagsProject",
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="filter by Tag name text",
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
     *         description="Tag response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Tag")
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
        $project = Project::where('user_id', Auth::user()->id)->where('id', $projectId)->first();

        $projectTags =  $project->tags()->paginate(10);

        // Search for summary text
        if ($request->has('search')) {
            $projectTags->where('name', $request->input('search'));
        }

        return (new TagCollection($projectTags))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Post(
     *     path="user/projects/{projectId}/tags",
     *     operationId="addTag to Project",
     *     description="Creates a new Tag for Project",
     *     summary="Create a new Tag for a Project",
     *     tags={"TagsProject"},
     *     @OA\Response(
     *         response=200,
     *         description="Tag response",
     *         @OA\JsonContent(ref="#/components/schemas/Tag")
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

        $tag = Tag::create([
            'user_id'           => Auth::user()->id,
            'name'              => $request->name,
            'description'       => $request->description,
            'rbg_color_code'    => $request->rbgColorCode,
            'created_by'        => $request->createdBy,
        ]);

        $project->tags()->attach([$tag->id]);

        $projectTag = $project->tags()->where('tags.id', $tag->id)->first();

        return (new TagResource($projectTag))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="user/projects/{projectId}/tags/{tagId}",
     *     summary="Get a single Tag based on the  projectId and tagId",
     *     tags={"TagsProject"},
     *     description="Returns a single Tag",
     *     operationId="showTag",
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
     *         name="tagId",
     *         in="path",
     *         description="id of the tag",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tag response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Tag")
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
    public function show($projectId, $tagId)
    {
        $project = Project::where('user_id', Auth::user()->id)->where('id', $projectId)->first();

        $projectTag = $project->tags()->where('tags.id', $tagId)->first();

        return (new TagResource($projectTag))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Put(
     *     path="user/projects/{projectId}/tags/{tagId}",
     *     operationId="edit Tag based on the Project",
     *     description="Edits a Tag based on the Project",
     *     summary="Edit a Tag based on the Project",
     *     tags={"TagsProject"},
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
     *         name="tagId",
     *         in="path",
     *         description="id of the tag",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Tag to edit",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/Tag")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tag response",
     *         @OA\JsonContent(ref="#/components/schemas/Tag")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function update(Request $request, $projectId, $tagId)
    {
        $validator = Validator::make($request->all(), self::validationArray());

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->messages()
            ], 400);
        }

        $project = Project::where('user_id', Auth::user()->id)->where('id', $projectId)->first();

        $projectTag = $project->tags()->where('tags.id', $tagId)->first();

        $projectTag->update([
            'user_id'           => Auth::user()->id,
            'name'              => $request->name,
            'description'       => $request->description,
            'rbg_color_code'    => $request->rbgColorCode,
            'created_by'        => $request->createdBy,
        ]);

        return (new TagResource($projectTag))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Delete(
     *     path="user/projects/{projectId}/tags/{tagId}",
     *     description="Delete a single Tag based on the  projectId",
     *     operationId="deleteTag based on the  projectId",
     *     summary="Delete a Tag based on the  projectId",
     *     tags={"TagsProject"},
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
     *         description="tagId",
     *         in="path",
     *         name="tagId",
     *         required=true,
     *         @OA\Schema(
     *             format="int64",
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tag response",
     *         @OA\JsonContent(ref="#/components/schemas/Tag")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function destroy($projectId, $tagId)
    {
        $project = Project::where('user_id', Auth::user()->id)->where('id', $projectId)->first();

        $projectTag = $project->tags()->where('tags.id', $tagId)->first();

        Taggable::where('tag_id', $tagId)->where('taggable_id', $projectId)
            ->where('taggable_type', 'App\Project')->delete();
        $projectTag->delete();

        return (new TagResource($projectTag))
            ->response()
            ->setStatusCode(200);
    }

    /*
    * Validation rules for saving to table
    */
    public static function validationArray():array {
        $validation = array (
            "name"         => 'required|string|max:255|unique:tags,name,user_id'.  Auth::user()->id,
            "description"  => 'required|string|max:255',
            "rbgColorCode" => 'required|string|max:255',
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
