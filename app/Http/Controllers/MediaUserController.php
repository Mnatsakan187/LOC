<?php

namespace App\Http\Controllers;

use App\Http\Resources\MediaCollection;
use App\Http\Resources\MediaResource;
use App\User;
use App\Media;
use App\Mediable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\Storage;


class MediaUserController extends Controller
{
    /**
     * @OA\Get(
     *     path="user/users/{userId}/medium",
     *     summary="Get a list of medium based on userId",
     *     tags={"MediumUser"},
     *     description="Returns all medium from the system that the user has access to.",
     *     operationId="findMediumUser",
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
    public function index(Request $request, $userId)
    {
        $user = User::where('user_id', Auth::user()->id)->where('id', $userId)->first();

        $userMedium =  $user->medium()->paginate(10);

        // Search for summary text
        if ($request->has('search')) {
            $userMedium->where('name', $request->input('search'));
        }

        return (new MediaCollection($userMedium))
            ->response()
            ->setStatusCode(200);
    } 

    /**
     * @OA\Post(
     *     path="user/users/{userId}/medium",
     *     operationId="addMedia to User",
     *     description="Creates a new Media for User",
     *     summary="Create a new Media for a User",
     *     tags={"MediumUser"},
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
    public function store(Request $request, $userId)
    {
        $validator = Validator::make($request->all(),  self::validationArray());

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->messages()
            ], 400);
        }

        $user = User::find($userId);

        $media = Media::create([
            'user_id'           => Auth::user()->id,
            'display_name'      => $request->displayName,
            'field_name'        => $request->fieldName,
            'uri'               => $request->uri,
            'created_by'        => $request->createdBy,
            'updated_by'        => $request->updatedBy,
            'media_type'        => $request->mediaType,
        ]);

        $user->medium()->attach([$media->id]);

        if($request->hasFile('profilePictureImage')){

            $image = $request->file('profilePictureImage');

            Storage::disk('public')->putFile('avatarImage/'.$user->id, $image);

            $avatarImage = $image->hashName();

            Media::where('id', $media->id)->update(['uri' => $avatarImage ]);

        }
        
        $userMedia = $user->medium()->where('media.id', $media->id)->first();

        return (new MediaResource($userMedia))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="user/users/{userId}/medium/{mediaId}",
     *     summary="Get a single Media based on the  userId and tagId",
     *     tags={"MediumUser"},
     *     description="Returns a single Media",
     *     operationId="showMedia",
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="id of the user",
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
    public function show($userId, $mediaId)
    {
        $user = User::where('user_id', Auth::user()->id)->where('id', $userId)->first();

        $userMedia = $user->medium()->where('media.id', $mediaId)->first();

        return (new MediaResource($userMedia))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Put(
     *     path="user/users/{userId}/medium/{mediaId}",
     *     operationId="edit Media based on the User",
     *     description="Edits a Media based on the User",
     *     summary="Edit a Media based on the User",
     *     tags={"MediumUser"},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="id of the user",
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
    public function update(Request $request, $userId, $mediaId)
    {
        $validator = Validator::make($request->all(), self::validationArray());

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->messages()
            ], 400);
        }

        $user = User::where('id', $userId)->first();

        $userMedia = $user->medium()->where('media.id', $mediaId)->first();

        $userMedia->update([
            'user_id'           => Auth::user()->id,
            'display_name'      => $request->displayName,
            'field_name'        => $request->fieldName,
            'uri'               => $request->uri,
            'created_by'        => $request->createdBy,
            'updated_by'        => $request->updatedBy,
            'media_type'        => $request->mediaType,
        ]);

        if($request->hasFile('profilePictureImage')){

            $image = $request->file('profilePictureImage');

            Storage::disk('public')->putFile('avatarImage/'.$user->id, $image);

            $avatarImage = $image->hashName();

            Media::where('id', $userMedia->id)->update(['uri' => $avatarImage ]);

        }


        $userMedia = $user->medium()->where('media.id', $mediaId)->first();

        return (new MediaResource($userMedia))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Delete(
     *     path="user/users/{userId}/medium/{mediaId}",
     *     description="Delete a single Media based on the  userId",
     *     operationId="deleteMedia based on the  userId",
     *     summary="Delete a Media based on the  userId",
     *     tags={"MediumUser"},
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
    public function destroy($userId, $mediaId)
    {
        $user = User::where('user_id', Auth::user()->id)->where('id', $userId)->first();

        $userMedia = $user->medium()->where('media.id', $mediaId)->first();

        Mediable::where('media_id', $mediaId)->where('mediable_id', $userId)
            ->where('mediable_type', 'App\User')->delete();
        $userMedia->delete();

        return (new MediaResource($userMedia))
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
