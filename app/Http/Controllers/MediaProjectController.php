<?php

namespace App\Http\Controllers;

use App\Http\Resources\MediaCollection;
use App\Http\Resources\MediaResource;
use App\Project;
use App\Media;
use App\Mediable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\Storage;

class MediaProjectController extends Controller
{
    /**
     * @OA\Get(
     *     path="user/projects/{projectId}/media",
     *     summary="Get a list of media based on projectId",
     *     tags={"MediumProject"},
     *     description="Returns all media from the system that the user has access to.",
     *     operationId="findMediumProject",
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="filter by Media name text",
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
     *         description="Media response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Media")
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

        $projectMedium =  $project->media()->paginate(10);

        // Search for summary text
        if ($request->has('search')) {
            $projectMedium->where('name', $request->input('search'));
        }

        return (new MediaCollection($projectMedium))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Post(
     *     path="user/projects/{projectId}/media",
     *     operationId="addMedia to Project",
     *     description="Creates a new Media for Project",
     *     summary="Create a new Media for a Project",
     *     tags={"MediumProject"},
     *     @OA\Response(
     *         response=200,
     *         description="Media response",
     *         @OA\JsonContent(ref="#/components/schemas/Media")
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

        $media = Media::create([
            'user_id'           => Auth::user()->id,
            'display_name'      => $request->displayName,
            'field_name'        => $request->fieldName,
            'uri'               => $request->uri,
            'created_by'        => $request->createdBy,
            'updated_by'        => $request->updatedBy,
            'media_type'        => $request->mediaType,
        ]);

        $project->media()->attach([$media->id]);
        if ($request->hasFile('imageUris')) {

            $image = $request->file('imageUris');

            Storage::disk('public')->putFile('projects/projectImage/media/'.$media->id, $image);

            Media::where('id', $media->id)->update(['uri' =>  $image->hashName()]);
        }

        $projectMedia = $project->media()->where('media.id', $media->id)->first();

        return (new MediaResource($projectMedia))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="user/projects/{projectId}/media/{mediaId}",
     *     summary="Get a single Media based on the  projectId and tagId",
     *     tags={"MediumProject"},
     *     description="Returns a single Media",
     *     operationId="showMedia",
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
     *         name="mediaId",
     *         in="path",
     *         description="id of the media",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Media response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Media")
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
    public function show($projectId, $mediaId)
    {
        $project = Project::where('user_id', Auth::user()->id)->where('id', $projectId)->first();

        $projectMedia = $project->media()->where('media.id', $mediaId)->first();

        return (new MediaResource($projectMedia))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Put(
     *     path="user/projects/{projectId}/media/{mediaId}",
     *     operationId="edit Media based on the Project",
     *     description="Edits a Media based on the Project",
     *     summary="Edit a Media based on the Project",
     *     tags={"MediumProject"},
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
     *         name="mediaId",
     *         in="path",
     *         description="id of the media",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Media to edit",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/Media")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Media response",
     *         @OA\JsonContent(ref="#/components/schemas/Media")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function update(Request $request, $projectId, $mediaId)
    {
        $validator = Validator::make($request->all(), self::validationArray());

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->messages()
            ], 400);
        }

        $project = Project::where('user_id', Auth::user()->id)->where('id', $projectId)->first();

        $projectMedia = $project->media()->where('media.id', $mediaId)->first();

        $projectMedia->update([
            'user_id'           => Auth::user()->id,
            'displayName'       => $request->displayName,
            'field_name'        => $request->fieldName,
            'created_by'        => $request->createdBy,
            'updated_by'        => $request->updatedBy,
            'media_type'        => $request->mediaType,
        ]);


        if ($request->hasFile('imageUris')) {
            $image = $request->file('imageUris');

            Storage::disk('public')->putFile('projects/projectImage/media/'.$projectMedia->id, $image);

            Media::where('id', $projectMedia->id)->update(['uri' =>  $image->hashName()]);
        }

        return (new MediaResource($projectMedia))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Delete(
     *     path="user/projects/{projectId}/media/{mediaId}",
     *     description="Delete a single Media based on the  projectId",
     *     operationId="deleteMedia based on the  projectId",
     *     summary="Delete a Media based on the  projectId",
     *     tags={"MediumProject"},
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
     *         description="mediaId",
     *         in="path",
     *         name="mediaId",
     *         required=true,
     *         @OA\Schema(
     *             format="int64",
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Media response",
     *         @OA\JsonContent(ref="#/components/schemas/Media")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function destroy($projectId, $mediaId)
    {
        $project = Project::where('user_id', Auth::user()->id)->where('id', $projectId)->first();

        $projectMedia = $project->media()->where('media.id', $mediaId)->first();

        Mediable::where('media_id', $mediaId)->where('mediable_id', $projectId)
            ->where('mediable_type', 'App\Project')->delete();
        $projectMedia->delete();

        return (new MediaResource($projectMedia))
            ->response()
            ->setStatusCode(200);
    }

    /*
    * Validation rules for saving to table
    */
    public static function validationArray():array {
        $validation = array (
            "displayName"   => 'required|string|max:255',
            "fieldName"     => 'required|string|max:255',
            "uri"           => 'required|string|max:255',
            "mediaType"     => 'required|integer|max:255',
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
