<?php

namespace App\Http\Controllers;

use App\Group;
use App\Http\Resources\GroupCollection;
use App\Http\Resources\GroupResource;
use App\Http\Resources\UserCollection;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use DB;
use Illuminate\Pagination\LengthAwarePaginator;

class GroupController extends Controller
{
    /**
     * @OA\Get(
     *     path="/user/groups",
     *     summary="Get a list of group",
     *     tags={"Groups"},
     *     description="Returns all group from the system that the user has access to.",
     *     operationId="findGroup",
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
     *         description="Group response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Group")
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

        $groups = Group::where('user_id', Auth::user()->id)->orderBy('id', 'desc');

        if(isset($request->perPage) && $request->perPage != ''){
            $page = $request->page;
            $perPage = $request->perPage;
            $group = $groups->get();
            $groups = new LengthAwarePaginator($group->forPage($page, $perPage), $group->count(), $perPage, $page);



        }else {
            $groups = $groups->get();
        }

        return (new GroupCollection($groups))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Post(
     *     path="/user/groups",
     *     operationId="addGroup",
     *     description="Creates a new group for user",
     *     summary="Create a new group  for a user",
     *     tags={"Groups"},
     *     @OA\Response(
     *         response=200,
     *         description="Group response",
     *         @OA\JsonContent(ref="#/components/schemas/Group")
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
        $validator = Validator::make($request->all(),  self::validationStoreArray());

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->messages()
            ], 400);
        }

        $group = Group::create([
            'user_id'          => Auth::user()->id,
            'name'             => $request->name,
            'description'      => $request->description,
            'created_by'       => Auth::user()->id,
            'updated_by'       => Auth::user()->id,
            'is_visible'       => $request->isVisible,
        ]);


        if(isset($request->user) && !empty($request->user)) {
            $ids = [];
            foreach ($request->user as $item) {
                $ids[] = $item['id'];
            }
            $group->users()->attach($ids);
        }

        return (new GroupResource($group))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="/user/groups/{groupId}",
     *     summary="Get a single group",
     *     tags={"Groups"},
     *     description="Returns a single group",
     *     operationId="showGroup",
     *     @OA\Parameter(
     *         name="groupId",
     *         in="path",
     *         description="id of the group",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Group response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Group")
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
    public function show($groupId)
    {
        $group = Group::where('id', $groupId)->first();

        return (new GroupResource($group))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Put(
     *     path="/user/groups/{groupId}",
     *     operationId="editGroup",
     *     description="Edits a group",
     *     summary="Edit a group",
     *     tags={"Groups"},
     *     @OA\Parameter(
     *         name="groupId",
     *         in="path",
     *         description="id of the groupId",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Group to edit",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/Group")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Group response",
     *         @OA\JsonContent(ref="#/components/schemas/Group")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function update(Request $request, $groupId)
    {
        $getGroup = Group::where('user_id', Auth::user()->id)->where('name', $request->name)->get();

        if($getGroup != null && count($getGroup->toArray()) == 1 && $getGroup['0']['id'] != (int)$groupId){
            return response()->json(
                [
                    'error' => ['name' => ['The name has already been taken.']]
                ], 400);
        }


        $group = Group::where('user_id', Auth::user()->id)->where('id', $groupId)->first();
        $group->update([
            'user_id'          => Auth::user()->id,
            'name'             => $request->name,
            'description'      => $request->description,
            'created_by'       => $request->createdBy,
            'updated_by'       => $request->updatedBy,
            'is_visible'       => $request->isVisible,
        ]);

        if(isset($request->user) && !empty($request->user)) {
            $ids = [];
            foreach ($request->user as $item) {
                $ids[] = $item['id'];
            }
            $group->users()->sync($ids);
        }

        return (new GroupResource($group))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Delete(
     *     path="/user/groups/{groupId}",
     *     description="Delete a single Group based on the  groupId",
     *     operationId="deleteGroup",
     *     summary="Delete a group",
     *     tags={"Groups"},
     *     @OA\Parameter(
     *         description="groupId",
     *         in="path",
     *         name="groupId",
     *         required=true,
     *         @OA\Schema(
     *             format="int64",
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Issue response",
     *         @OA\JsonContent(ref="#/components/schemas/Group")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function destroy($groupId)
    {
        $group = Group::where('user_id', Auth::user()->id)->where('id', $groupId)->first();

        DB::table('group_user')->where('group_id', '=', $groupId)->delete();

        $group->delete();

        return (new GroupResource($group))
            ->response()
            ->setStatusCode(200);
    }



    public function getUserGroupSubscription()
    {
        $subscriptions = Auth::user()->subscriptions()->orderBy('id', 'desc')->get();
        $subscriptionGroup = 0;
        if($subscriptions && !empty($subscriptions)){
            foreach ($subscriptions  as $subscription){
                if($subscription){
                    if($subscription->plan_id == 1){
                        $subscriptionGroup += 1;
                    }elseif ($subscription->plan_id == 2){
                        $subscriptionGroup += 3;
                    }elseif ($subscription->plan_id == 3){
                        $subscriptionGroup += 5;
                    }
                }
            }
        }else{
            return response()->json([
                'subscription' => false,
                'newProfile' => false,
            ]);
        }

        $groups = Auth::user()->groupsUser()->count();


        if($groups  < $subscriptionGroup ){
            return response()->json([
                'subscription' => true,
                'newProfile' => true,
            ]);
        }else{
            return response()->json([
                'subscription' => true,
                'newProfile' => false,
            ]);
        }
    }


    public function destroyGroupMember($groupId, $memberId)
    {
        $group = Group::where('user_id', Auth::user()->id)->where('id', $groupId)->first();
        $group->users()->detach([$memberId]);

        return (new GroupResource($group))
            ->response()
            ->setStatusCode(200);
    }


    public function storeMember(Request $request, $groupId)
    {
        $group = Group::where('user_id', Auth::user()->id)->where('id', $groupId)->first();
        $ids = [];
        if(!empty($request->user )){
            foreach ($request->user as $item) {
                $ids[] = $item['id'];
            }
            $group->users()->attach($ids);
        }

        return (new GroupResource($group))
            ->response()
            ->setStatusCode(200);
    }


    public function groupMembers($groupId)
    {
        $group = Group::where('user_id', Auth::user()->id)->where('id', $groupId)->first();
        $users =  $group->users()->get();

        return (new UserCollection($users))
            ->response()
            ->setStatusCode(200);
    }





    /*
     * Validation rules for saving to table
    */
    public static function validationStoreArray():array {
        $validation = array (
            "name"      => 'required|string|max:255|unique:groups,name,NULL,id,user_id,'.Auth::user()->id,
            "isVisible" => 'required|integer',
        );

        return $validation;
    }

    /*
    * Validation rules for updating to table
   */
    public static function validationUpdateArray($groupId):array {
        $validation = array (
            "name" => 'required|max:255|unique:groups,name,'.$groupId,
            "isVisible" => 'required|integer',
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
