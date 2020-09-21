<?php

namespace App\Http\Controllers;

use App\Collection;
use App\Connect;
use App\Event;
use App\Http\Resources\EventResource;
use App\Http\Resources\PollResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\ProfileCollection;
use App\Http\Resources\ProfileResource;
use App\Http\Resources\ProjectResource;
use App\Poll;
use App\Post;
use App\Profile;
use App\Project;
use App\SocialMediaLink;
use App\Teamable;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\Storage;
use DB;

class ProfileController extends Controller
{
    /**
     * @OA\Get(
     *     path="/user/profiles",
     *     summary="Get a list of profile",
     *     tags={"Profiles"},
     *     description="Returns all profile from the system that the user has access to.",
     *     operationId="findProject",
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
     *
     */
    public function index(Request $request)
    {

        $profiles = Profile::where('user_id', Auth::user()->id);

        // Search for summary text
        if ($request->has('search')) {
            $profiles->where('creative_title', $request->input('search'));
        }

        $profiles = $profiles->paginate(10);

        return (new ProfileCollection($profiles))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Post(
     *     path="/user/profiles",
     *     operationId="addProject",
     *     description="Creates a new profile for user",
     *     summary="Create a new profile for a user",
     *     tags={"Profiles"},
     *     @OA\Response(
     *         response=200,
     *         description="Profile response",
     *         @OA\JsonContent(ref="#/components/schemas/Profile")
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


        $profile = Profile::create([
            'user_id'                       => Auth::user()->id,
            'creative_title'                => $request->creativeTitle,
            'biography'                     => $request->biography,
            'location'                      => $request->location,
            'avatar_uri'                    => $request->file('avatarUri') ? $request->file('avatarUri')->hashName(): '',
            'background_uri'                => $request->file('backgroundUri') ? $request->file('backgroundUri')->hashName(): '',

        ]);


        if ($request->hasFile('avatarUri')) {
            $image = $request->file('avatarUri');

            Storage::disk('public')->putFile('profiles/profilePictureImage/'.$profile->id, $image);
        }

        if ($request->hasFile('backgroundUri')) {
            $image = $request->file('backgroundUri');

            Storage::disk('public')->putFile('profiles/profileBackgroundImage/'.$profile->id, $image);
        }


        if(isset($request->socials) && !empty($request->socials)){
            $socialsArray = json_decode($request->socials);
            $arr = [];
            foreach ($socialsArray as $key => $value){
                foreach ($value as $iKey  => $iValue){
                    $arr[] = [
                        'user_id'           => Auth::user()->id,
                        'profile_id'        => $profile->id,
                        'name'              => $iKey,
                        'social_media_link' => $iValue,
                        'created_at'        => Carbon::now()->toDateTimeString(),
                        'updated_at'        => Carbon::now()->toDateTimeString(),
                    ];
                }
            }

            SocialMediaLink::insert($arr);
        }

        return (new ProfileResource($profile))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="/user/profiles/{profileId}",
     *     summary="Get a single profile",
     *     tags={"Profiles"},
     *     description="Returns a single profile",
     *     operationId="showProfile",
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

    public function show($profileId)
    {
        $profile = Profile::where('id', $profileId)->first();

        return (new ProfileResource($profile))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Put(
     *     path="/user/profiles/{profileId}",
     *     operationId="editProject",
     *     description="Edits a profile",
     *     summary="Edit a profile",
     *     tags={"Profiles"},
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
     *     @OA\RequestBody(
     *         description="User to edit",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/Profile")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Event response",
     *         @OA\JsonContent(ref="#/components/schemas/Profile")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function update(Request $request, $profileId)
    {
        $getProfile = Profile::where('id', $profileId)->where('creative_title', $request->creativeTitle)->get();

        if($getProfile != null && count($getProfile->toArray()) == 1 && $getProfile['0']['id'] != (int)$profileId){
            return response()->json(
                [
                    'error' => ['name' => ['The creative title has already been taken.']]
                ], 400);
        }


        $profile = Profile::where('user_id', Auth::user()->id)->where('id', $profileId)->first();
        $profile->update([
            'user_id'                       => Auth::user()->id,
            'creative_title'                => $request->creativeTitle,
            'biography'                     => $request->biography,
            'location'                      => $request->location,
        ]);



        if ($request->hasFile('avatarUri')) {

            if($profile->avatar_uri){

                Storage::disk('public')->delete('profiles/profilePictureImage/'.$profile->id. '/'.$profile->avatar_uri);
            }

            $image = $request->file('avatarUri');

            Storage::disk('public')->putFile('profiles/profilePictureImage/'.$profile->id, $image);

            Profile::where('id', $profileId)->update(['avatar_uri' =>  $image->hashName()]);
        }



        if ($request->hasFile('backgroundUri')) {

            if($profile->background_uri){

                Storage::disk('public')->delete('profiles/profileBackgroundImage/'.$profile->id. '/'.$profile->background_uri);
            }

            $image = $request->file('backgroundUri');

            Storage::disk('public')->putFile('profiles/profileBackgroundImage/'.$profile->id, $image);

            Profile::where('id', $profileId)->update(['background_uri' =>  $image->hashName()]);
        }

        if(isset($request->socials) && !empty($request->socials)){
            SocialMediaLink::where('profile_id', $profileId)->delete();
            $socialsArray = json_decode($request->socials);
            $arr = [];
            foreach ($socialsArray as $key => $value){
                foreach ($value as $iKey  => $iValue){
                    $arr[] = [
                        'user_id'           => Auth::user()->id,
                        'profile_id'        => $profile->id,
                        'name'              => $iKey,
                        'social_media_link' => $iValue,
                        'created_at'        => Carbon::now()->toDateTimeString(),
                        'updated_at'        => Carbon::now()->toDateTimeString(),
                    ];
                }
            }

            SocialMediaLink::insert($arr);
        }

        $profile = Profile::where('id', $profileId)->first();

        return (new ProfileResource($profile))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Delete(
     *     path="/user/profiles/{profileId}",
     *     description="Delete a single Project based on the  profileId",
     *     operationId="deleteProject",
     *     summary="Delete a profile",
     *     tags={"Profiles"},
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
    public function destroy($profileId)
    {
        $profile = Profile::where('user_id', Auth::user()->id)->where('id', $profileId)->first();


        //Delete profile projects
        $projects = $profile->project()->get();
        if(!empty($projects)){
            foreach ($projects as $project){

                if($project->avatar_uri){
                    Storage::disk('public')->delete('projects/projectAvatar/'.$project->id. '/'.$project->avatar_uri);
                }

                if($project->background_uri){
                    Storage::disk('public')->delete('projects/projectBackgroundImg/'.$project->id. '/'.$project->background_uri);
                }

                if($project->original_background_uri){
                    Storage::disk('public')->delete('projects/projectOriginalBackgroundImg/'.$project->id. '/'.$project->original_background_uri);
                }
                $project->delete();
            }
        }


        //Delete profile polls
        $polls = $profile->poll()->get();
        if(!empty($polls->toArray())){
            foreach ($polls as $poll){
                if(!$poll->project_id){
                    $poll->answers()->delete();
                    $poll->pollAnswer()->delete();
                    $poll->delete();
                }
            }
        }

        //Delete profile posts
        $posts = $profile->post()->get();
        if(!empty($posts)){
            foreach ($posts as $post){
                if(!$post->project_id && !$post->group_id){

                    $postImages = $post->media()->where('media_type', 0)->get();

                    if(!empty($postImages->toArray())){

                        foreach ($postImages as $image){
                            Storage::disk('public')->delete('posts/mediaPostImage/media/'.$image->id, $image->uri);
                        }
                    }

                    $post->delete();
                }
            }

        }

        Teamable::where('teamable_id', '=', $profileId)->delete();

        SocialMediaLink::where('profile_id', '=', $profileId)->delete();


        if($profile->avatar_uri){
            Storage::disk('public')->delete('profiles/profilePictureImage/'.$profile->id.'/'.$profile->avatar_uri);
        }

        if($profile->background_uri){
            Storage::disk('public')->delete('profiles/profileBackgroundImage/'.$profile->id.'/'.$profile->background_uri);
        }

        $profile->delete();

        return (new ProfileResource($profile))
            ->response()
            ->setStatusCode(200);
    }



    public function getUserProfileSubscription()
    {
        $subscriptions = Auth::user()->subscriptions()->orderBy('id', 'desc')->get();
        $subscriptionProfiles = 0;
        if($subscriptions && !empty($subscriptions)){
            foreach ($subscriptions  as $subscription){
                if($subscription){
                    if($subscription->plan_id == 1){
                        $subscriptionProfiles += 1;
                    }elseif ($subscription->plan_id == 2){
                        $subscriptionProfiles += 4;
                    }elseif ($subscription->plan_id == 3){
                        $subscriptionProfiles += 5;
                    }
                }
            }
        }else{
            return response()->json([
                'subscription' => false,
                'newProfile' => false,
            ]);
        }

        $profiles = Auth::user()->profiles()->count();

        if($profiles < $subscriptionProfiles ){
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

    public function updateProfileColumn($profileId)
    {
        $profile = Profile::where('id', $profileId)->first();
        Profile::where('id', $profileId)->update(['updated' => 0, 'updated_color' => 0]);
        $collecrtions = $profile->collections()->get();
        foreach ($collecrtions as $collecrtion){
            Collection::where('id', $collecrtion->id)->update(['updated' => 0]);
        }

    }

    public function updateHide(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $requestId = $request->id;
        $hideable = [
            'hideable_id' => null,
            'hideable_type' => null
        ];

        if($request->type == 'profile'){

            /** @var Profile $profile */
            $profile = Profile::findOrFail($requestId);
            $hideable['hideable_id'] = $profile->getKey();
            $hideable['hideable_type'] = Profile::class;

        }elseif ($request->type == 'project'){

            /** @var Project $project */
            $project = Project::findOrFail($requestId);
            $hideable['hideable_id'] = $project->getKey();
            $hideable['hideable_type'] = Project::class;

        }elseif ($request->type == 'event'){

            /** @var Event $event */
            $event = Event::findOrFail($requestId);
            $hideable['hideable_id'] = $event->getKey();
            $hideable['hideable_type'] = Event::class;

        }

        if ($hideable['hideable_id'] && $hideable['hideable_type']) {
            $user->hides()->create($hideable);
        }

    }

    public function profileView(Request $request, $profileId)
    {

        $projects = Project::where('profile_id', $profileId)->get();
        $events   = Event::where('profile_id', $profileId)->get();
        $posts    = Post::where('profile_id', $profileId)->get();
        $polls    = Poll::where('profile_id', $profileId)->get();

        $timeline = collect();
        foreach ($projects as $project) {
            $timeline->push(new ProjectResource($project));
        }

        foreach ($events as $event) {
            $timeline->push(new EventResource($event));
        }

        foreach ($posts as $post) {
            $timeline->push(new PostResource($post));
        }

        foreach ($polls as $poll) {
            $timeline->push(new PollResource($poll));
        }

        $timelineSort = $timeline->sortByDesc('updated_at');

        $sortedTimeline = collect();
        foreach ($timelineSort as $item){
            $sortedTimeline->push($item);
        }

        return response($sortedTimeline)
            ->setStatusCode(200);
    }

    /*
   * Validation rules for saving to table
   */
    public static function validationStoreArray():array {
        $validation = array (
            'creativeTitle' => 'required|string|max:255|unique:profiles,creative_title,NULL,id,user_id,'.Auth::user()->id,
        );

        return $validation;
    }

    /*
    * Validation rules for update to table
    */
    public static function validationUpdateArray($profileId):array {
        $validation = array (
            "creativeTitle" => 'required|max:255|unique:profiles,creative_title,NULL,id,user_id,'.$profileId,
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
