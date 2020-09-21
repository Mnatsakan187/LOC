<?php

namespace App\Http\Controllers;

use App\Collection;
use App\Http\Resources\CollectionCollection;
use App\Http\Resources\CollectionResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\Storage;

class CollectionController extends Controller
{
    /**
     * @OA\Get(
     *     path="/user/collections",
     *     summary="Get a list of collections",
     *     tags={"Collections"},
     *     description="Returns all collections from the system that the user has access to.",
     *     operationId="findCollections",
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="filter by name text",
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
     *         description="Collection response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Collection")
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
    public function index(Request $request)
    {
        $collections = Collection::where('user_id', Auth::user()->id);

        // Search for summary text
        if ($request->has('search') && $request->search) {
            $collections =  $collections->where('name', 'like', '%'. $request->input('search'). '%');
        }

        $collections = $collections->paginate(10);

        return ( new CollectionCollection($collections))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Post(
     *     path="/user/collections",
     *     operationId="addCollection",
     *     description="Creates a new Collection for user",
     *     summary="Create a new Collection for a user",
     *     tags={"Collections"},
     *     @OA\Response(
     *         response=200,
     *         description="Collection response",
     *         @OA\JsonContent(ref="#/components/schemas/Collection")
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

        $collection = Collection::create([
            'user_id'           => Auth::user()->id,
            'name'              => $request->name,
            'updated_by'        =>  Auth::user()->id,
            'profile_id'        =>  $profileId,
            'background_uri'    => $request->file('backgroundUri') ? $request->file('backgroundUri')->hashName(): '',
        ]);

        if ($request->hasFile('backgroundUri')) {
            $image = $request->file('backgroundUri');

            Storage::disk('public')->putFile('collections/'.$collection->id, $image);
        }

        return (new CollectionResource($collection))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="/user/collections/{collectionId}",
     *     summary="Get a single collection",
     *     tags={"Collections"},
     *     description="Returns a single collection",
     *     operationId="showCollection",
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
     *     @OA\Response(
     *         response=200,
     *         description="Collection response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Collection")
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
    public function show($profileId, $collectionId)
    {
        $collection = Collection::where('user_id', Auth::user()->id)
            ->where('id', $collectionId)
            ->first();

        return ( new CollectionResource($collection))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Put(
     *     path="/user/collections/{collectionId}",
     *     operationId="editCollection",
     *     description="Edits a collection",
     *     summary="Edit a collection",
     *     tags={"Collections"},
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
     *     @OA\RequestBody(
     *         description="Collection to edit",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/Collection")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Collection response",
     *         @OA\JsonContent(ref="#/components/schemas/Collection")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function update(Request $request, $profileId,  $collectionId)
    {
        $getCollection = Collection::where('user_id', Auth::user()->id)->where('name', $request->name)->get();

        if($getCollection != null && count($getCollection->toArray()) == 1 && $getCollection['0']['id'] != (int)$collectionId){
            return response()->json(
                [
                    'error' => ['name' => ['The name has already been taken.']]
                ], 400);
        }


        $collection = Collection::where('user_id', Auth::user()->id)->where('id', $collectionId)->first();
        $collection->update([
            'user_id'          => Auth::user()->id,
            'name'             => $request->name,
            'updated_by'       => $request->updatedBy,
            'profile_id'        =>  $profileId,
        ]);

        if ($request->hasFile('backgroundUri')) {
            $image = $request->file('backgroundUri');

            Storage::disk('public')->putFile('collections/'.$collection->id, $image);
        }

        if ($request->hasFile('backgroundUri')) {

            if($collection->background_uri){
                Storage::disk('public')->delete('collections/'.$collection->id. '/'.$collection->background_uri);
            }

            $image = $request->file('backgroundUri');

            Storage::disk('public')->putFile('collections/'.$collection->id, $image);

            Collection::where('id', $collectionId)->update(['background_uri' =>  $image->hashName()]);
        }

        return ( new CollectionResource($collection))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Delete(
     *     path="/user/collections/{collectionId}",
     *     description="Delete a single Collection based on the  collectionId",
     *     operationId="deleteCollection",
     *     summary="Delete a collection",
     *     tags={"Collections"},
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
     *     @OA\Response(
     *         response=200,
     *         description="Collection response",
     *         @OA\JsonContent(ref="#/components/schemas/Collection")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function destroy($profileId, $collectionId)
    {
        $collection = Collection::where('user_id', Auth::user()->id)
            ->where('id', $collectionId)
            ->first();

        if($collection->background_uri){
            Storage::disk('public')->delete('collections/'.$collection->id. '/'.$collection->background_uri);
        }

        $collection->delete();

        return ( new CollectionResource($collection))
            ->response()
            ->setStatusCode(200);
    }

   /*
    * Validation rules for saving to table
    */
    public static function validationArray():array {

        $validation = array (
            'name' => 'required|string|max:255|unique:collections,name,NULL,id,user_id,'.Auth::user()->id,
        );

        return $validation;
    }

    /*
    * Validation rules for updating to table
    */
    public static function validationArrayUpdate():array {
        $validation = array (
            "name" => 'required|string|max:255|unique:projects,name,user_id'.  Auth::user()->id,
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
