<?php

namespace App\Http\Controllers;

use App\Collection;
use App\Http\Resources\CollectionCollection;
use App\Http\Resources\CollectionResource;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class CollectionCreatorController extends Controller
{
    /**
     * @OA\Get(
     *     path="/user/collections/{collectionId}/creators",
     *     summary="Get a list of collection  of creators",
     *     tags={"CollectionCreators"},
     *     description="Returns all creators from the collections",
     *     operationId="findCreators",
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
    public function index(Request $request, $collectionId)
    {
        $collection = Collection::where('user_id', Auth::user()->id)
            ->where('id', $collectionId)->first();
        $creators = $collection->creators()->paginate(10);

        return ( new UserCollection($creators))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Post(
     *     path="user/collections/{collectionId}/creators/{creatorId}",
     *     operationId="add",
     *     description="Add creator to collections",
     *     summary="Add creator to collections",
     *     tags={"CollectionCreators"},
     *     @OA\Response(
     *         response=200,
     *         description="Collection response",
     *         @OA\JsonContent(ref="#/components/schemas/User")
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
    public function store(Request $request, $collectionId, $creatorId)
    {
        $collection = Collection::findorFail($collectionId);

        $collection->creators()->attach($creatorId);

        $creator = $collection->creators()->where('users.id', $creatorId)->first();

        return (new UserResource($creator))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="/user/collections/{collectionId}/creators/{creatorId}",
     *     summary="Get a single creator from collection",
     *     tags={"CollectionCreators"},
     *     description="Returns a single creator from collection",
     *     operationId="showCreator",
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
     *         name="creatorId",
     *         in="path",
     *         description="id of the creator",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Creator response",
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
    public function show($collectionId, $creatorId)
    {
        $collection = Collection::where('user_id', Auth::user()->id)->where('id', $collectionId)->first();
        $creator = $collection->creators()->where('users.id', $creatorId)->first();

        return ( new UserResource($creator))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Delete(
     *     path="/user/collections/{collectionId}/creators/{creatorId}",
     *     description="Delete a single creator  from collection",
     *     operationId="deleteCreator",
     *     summary="Delete a creator from collection",
     *     tags={"CollectionCreators"},
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
     *         description="creatorId",
     *         in="path",
     *         name="creatorId",
     *         required=true,
     *         @OA\Schema(
     *             format="int64",
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Creator response",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function destroy($collectionId, $creatorId)
    {
        $collection = Collection::findorFail($collectionId);

        $creator = $collection->creators()->where('users.id', $creatorId)->first();

        Collection::where('id', $collection->id)->update(['updated' => 1,  'updated_at' => Carbon::now()->toDateTimeString()]);

        $collection->creators()->detach($creatorId);

        return ( new UserResource($creator))
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
            'creatorId.required' => 'This  creator already belong to this collection',
        );
        return $validationMessages;
    }
}
