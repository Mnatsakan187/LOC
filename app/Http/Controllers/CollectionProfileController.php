<?php

namespace App\Http\Controllers;

use App\Collection;

use App\Http\Resources\ProfileCollection;
use App\Http\Resources\ProfileResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class CollectionProfileController extends Controller
{
    /**
     * @OA\Get(
     *     path="/user/collections/{collectionId}/profiles",
     *     summary="Get a list of collection  of profiles",
     *     tags={"CollectionProfiles"},
     *     description="Returns all profiles from the collections",
     *     operationId="findProfiles",
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
     *             @OA\Items(ref="#/components/schemas/Profile")
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

        $profiles = $collection->profiles()->paginate(10);

        return ( new ProfileCollection($profiles))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Post(
     *     path="user/collections/{collectionId}/profiles/{profileId}",
     *     operationId="add",
     *     description="Add profile to collections",
     *     summary="Add profile to collections",
     *     tags={"CollectionProfiles"},
     *     @OA\Response(
     *         response=200,
     *         description="Collection response",
     *         @OA\JsonContent(ref="#/components/schemas/Profile")
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
    public function store(Request $request, $collectionId, $profileId)
    {
        $collection = Collection::findorFail($collectionId);

        $collection->profiles()->attach($profileId);

        Collection::where('id', $collection->id)->update(['updated' => 1,  'updated_at' => Carbon::now()->toDateTimeString()]);

        $profile = $collection->profiles()->where('profiles.id', $profileId)->first();

        return (new ProfileResource($profile))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="/user/collections/{collectionId}/profiles/{profileId}",
     *     summary="Get a single profile from collection",
     *     tags={"CollectionProfiles"},
     *     description="Returns a single profile from collection",
     *     operationId="showProfile",
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
     *         name="profileId",
     *         in="path",
     *         description="id of the profile",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Profile")
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
    public function show($collectionId, $profileId)
    {
        $collection = Collection::where('user_id', Auth::user()->id)->where('id', $collectionId)->first();
        $profile = $collection->profiles()->where('profiles.id', $profileId)->first();

        return ( new ProfileResource($profile))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Delete(
     *     path="/user/collections/{collectionId}/profiles/{profileId}",
     *     description="Delete a single profile  from collection",
     *     operationId="deleteProfile",
     *     summary="Delete a profile from collection",
     *     tags={"CollectionProfiles"},
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
     *         description="profileId",
     *         in="path",
     *         name="profileId",
     *         required=true,
     *         @OA\Schema(
     *             format="int64",
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile response",
     *         @OA\JsonContent(ref="#/components/schemas/Profile")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function destroy($collectionId, $profileId)
    {
        $collection = Collection::findorFail($collectionId);

        $profile = $collection->profiles()->where('profiles.id', $profileId)->first();

        $collection->profiles()->detach($profileId);

        return ( new ProfileResource($profile))
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
            'profileId.required' => 'This  profile already belong to this collection',
        );
        return $validationMessages;
    }
}
