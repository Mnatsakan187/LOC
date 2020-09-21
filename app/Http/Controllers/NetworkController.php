<?php

namespace App\Http\Controllers;

use App\Http\Resources\EventCollection;
use App\Http\Resources\EventResource;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Network;
use Carbon\Carbon;
use function GuzzleHttp\Promise\all;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Event;
use Illuminate\Support\Facades\Storage;

class NetworkController extends Controller
{
    /**
     * @OA\Get(
     *     path="/user/networks",
     *     summary="Get a list of networks",
     *     tags={"Networks"},
     *     description="Returns all networks from the system that the user has access to.",
     *     operationId="findNetwork",
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
     *         description="Network response",
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
    public function index(Request $request)
    {
        $networks = Auth::user()->netWorks();

        // Search for summary text
        if ($request->has('search')) {
            $networks->where('first_name', $request->input('search'));
        }

        $networks = $networks->get();

        return (new UserCollection($networks))
            ->response()
            ->setStatusCode(200);
    }


    /**
     * @OA\Post(
     *     path="/user/networks",
     *     operationId="addNetwork",
     *     description="Creates a new network for user",
     *     summary="Create a new network  for a user",
     *     tags={"Networks"},
     *     @OA\Response(
     *         response=200,
     *         description="Network response",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function store(Request $request)
    {


        $validator = Validator::make($request->all(),  self::validationArray());

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->messages()
            ], 400);
        }

        $array = [];
        if(!empty($request->networkUserId) && $request->networkUserId){
            foreach ($request->networkUserId as $item){
                $array[] = [
                    'user_id' => Auth::user()->id,
                    'network_user_id' => $item['id'],
                ];
            }
        }

        $network = Network::insert($array);

        return (new UserResource($network))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="/user/networks/{networkId}/show",
     *     summary="Get a single network",
     *     tags={"Networks"},
     *     description="Returns a single network",
     *     operationId="showNetwork",
     *     @OA\Parameter(
     *         name="networkId",
     *         in="path",
     *         description="id of the network",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Network response",
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

    public function show($networkId)
    {
        $networks = Auth::user()->netWorks()
            ->where('networks.id', '=', $networkId)->first();

        return (new UserResource($networks))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Put(
     *     path="/user/networks/{networkId}",
     *     operationId="editNetwork",
     *     description="Edits a network",
     *     summary="Edit a network",
     *     tags={"Networks"},
     *     @OA\Parameter(
     *         name="networkId",
     *         in="path",
     *         description="id of the network",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Network to edit",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Event response",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function update(Request $request, $networkId)
    {
        $validator = Validator::make($request->all(), self::validationArray());

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->messages()
            ], 400);
        }

        $network = Auth::user()->netWorks()
            ->where('networks.id', '=', $networkId)
            ->first();
        $network->update([
            'user_id'            => Auth::user()->id,
            'network_user_id'    => $request->networkUserId
        ]);

        return (new UserResource($event))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Delete(
     *     path="/user/networks/{networkId}",
     *     description="Delete a single network based on the  networkId",
     *     operationId="deleteNetwork",
     *     summary="Delete a network",
     *     tags={"Networks"},
     *     @OA\Parameter(
     *         description="networkId",
     *         in="path",
     *         name="networkId",
     *         required=true,
     *         @OA\Schema(
     *             format="int64",
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Network response",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function destroy($networkId)
    {
        $network = Network::where('networks.user_id', '=',  Auth::user()->id)
            ->where('networks.network_user_id', '=', $networkId)
            ->first();

        $network->delete();

        return (new UserResource($network))
            ->response()
            ->setStatusCode(200);
    }

    /*
    * Validation rules for saving to table
    */
    public static function validationArray():array {
        $validation = array (
            'networkUserId' => 'required',
        );

        return $validation;
    }

    /*
     * Validation messages
     */
    public static function validationMessages():array {
        $validationMessages = array (
            'networkUserId.required' => 'This network user id  field is required',
        );
        return $validationMessages;
    }
}
