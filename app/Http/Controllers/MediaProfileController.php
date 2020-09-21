<?php

namespace App\Http\Controllers;

use App\Http\Resources\MediaCollection;
use App\Http\Resources\MediaResource;
use App\Profile;
use App\Media;
use App\Mediable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\Storage;


class MediaProfileController extends Controller
{
    /**
     * @OA\Get(
     *     path="user/profiles/{profileId}/medium",
     *     summary="Get a list of medium based on profileId",
     *     tags={"MediumProfile"},
     *     description="Returns all medium from the system that the user has access to.",
     *     operationId="findMediumProfile",
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
    public function index(Request $request, $profileId)
    {
        $profile = Profile::where('user_id', Auth::user()->id)->where('id', $profileId)->first();

        $profileMedium =  $profile->medium()->paginate(10);

        // Search for summary text
        if ($request->has('search')) {
            $profileMedium->where('name', $request->input('search'));
        }

        return (new MediaCollection($profileMedium))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Post(
     *     path="user/profiles/{profileId}/medium",
     *     operationId="addMedia to Profile",
     *     description="Creates a new Media for Profile",
     *     summary="Create a new Media for a Profile",
     *     tags={"MediumProfile"},
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
    public function store(Request $request, $profileId)
    {

        $validator = Validator::make($request->all(),  self::validationArray());

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->messages()
            ], 400);
        }

        $profile = Profile::find($profileId);

        $media = Media::create([
            'user_id'           => Auth::user()->id,
            'display_name'      => $request->displayName,
            'field_name'        => $request->fieldName,
            'uri'               => $request->uri,
            'created_by'        => $request->createdBy,
            'updated_by'        => $request->updatedBy,
            'media_type'        => $request->mediaType,
        ]);

        $profile->medium()->attach([$media->id]);

        if ($request->hasFile('profilePictureImage')) {

            $image = $request->file('profilePictureImage');

            Storage::disk('public')->putFile('profiles/profilePictureImage/'.$profile->id, $image);

            $profilePictureImage = $image->hashName();

            Media::where('id', $media->id)->update(['uri' => $profilePictureImage ]);
        }


        if ($request->hasFile('profileBackgroundImage')) {

            $image = $request->file('profileBackgroundImage');

            Storage::disk('public')->putFile('profiles/profileBackgroundImage/'.$profile->id, $image);

            $profileBackgroundImageName = $image->hashName();

            Media::where('id', $media->id)->update(['uri' => $profileBackgroundImageName]);
        }

        $profileMedia = $profile->medium()->where('media.id', $media->id)->first();

        return (new MediaResource($profileMedia))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="user/profiles/{profileId}/medium/{mediaId}",
     *     summary="Get a single Media based on the  profileId and tagId",
     *     tags={"MediumProfile"},
     *     description="Returns a single Media",
     *     operationId="showMedia",
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
    public function show($profileId, $mediaId)
    {
        $profile = Profile::where('user_id', Auth::user()->id)->where('id', $profileId)->first();

        $profileMedia = $profile->medium()->where('media.id', $mediaId)->first();

        return (new MediaResource($profileMedia))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Put(
     *     path="user/profiles/{profileId}/medium/{mediaId}",
     *     operationId="edit Media based on the Profile",
     *     description="Edits a Media based on the Profile",
     *     summary="Edit a Media based on the Profile",
     *     tags={"MediumProfile"},
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
    public function update(Request $request, $profileId, $mediaId)
    {
        $validator = Validator::make($request->all(), self::validationArray());

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->messages()
            ], 400);
        }

        $profile = Profile::where('user_id', Auth::user()->id)->where('id', $profileId)->first();

        $profileMedia = $profile->medium()->where('media.id', $mediaId)->first();

        $profileMedia->update([
            'user_id'           => Auth::user()->id,
            'display_name'      => $request->displayName,
            'field_name'        => $request->fieldName,
            'uri'               => $request->uri,
            'created_by'        => $request->createdBy,
            'updated_by'        => $request->updatedBy,
            'media_type'        => $request->mediaType,
        ]);

        if ($request->hasFile('profilePictureImage')) {

            if($profile->profilePictureImage){
                Storage::disk('public')->delete('profiles/profilePictureImage/'.$profile->id. '/'.$profile->profilePictureImage);
            }

            $image = $request->file('profilePictureImage');

            Storage::disk('public')->putFile('profiles/profilePictureImage/'.$profile->id, $image);

            $profilePictureImage = $image->hashName();

            Media::where('id', $profileMedia->id)->update(['uri' => $profilePictureImage ]);
        }

        if ($request->hasFile('profileBackgroundImage')) {

            if($profileMedia->profileBackgroundImage){
                Storage::disk('public')->delete('profiles/profileBackgroundImage/'.$profile->id. '/'.$profile->profileBackgroundImage);
            }

            $image = $request->file('profileBackgroundImage');

            Storage::disk('public')->putFile('profiles/profileBackgroundImage/'.$profile->id, $image);

            $profileBackgroundImageName = $image->hashName();

            Media::where('id', $profileMedia->id)->update(['uri' => $profileBackgroundImageName ]);
        }

        return (new MediaResource($profileMedia))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Delete(
     *     path="user/profiles/{profileId}/medium/{mediaId}",
     *     description="Delete a single Media based on the  profileId",
     *     operationId="deleteMedia based on the  profileId",
     *     summary="Delete a Media based on the  profileId",
     *     tags={"MediumProfile"},
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
    public function destroy($profileId, $mediaId)
    {
        $profile = Profile::where('user_id', Auth::user()->id)->where('id', $profileId)->first();

        $profileMedia = $profile->medium()->where('media.id', $mediaId)->first();

        Mediable::where('media_id', $mediaId)->where('mediable_id', $profileId)
            ->where('mediable_type', 'App\Profile')->delete();
        $profileMedia->delete();

        return (new MediaResource($profileMedia))
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
