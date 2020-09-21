<?php

namespace App\Http\Controllers;

use App\Collection;
use App\Connect;
use App\Http\Resources\ProfileCollection;
use App\Http\Resources\ProjectCollection;
use App\Http\Resources\ProjectResource;
use App\Likeable;
use App\Mediable;
use App\Profile;
use App\Project;
use App\ProjectCoCreator;
use App\Rules\ProjectNameRule;
use App\Tag;
use App\Teamable;
use App\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use function GuzzleHttp\Promise\all;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    /**
     * @OA\Get(
     *     path="/user/profiles/{profileId}/projects",
     *     summary="Get a list of project",
     *     tags={"Projects"},
     *     description="Returns all project from the system that the user has access to.",
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
     *         description="Project response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Project")
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
        $projects = Project::where('profile_id', $profileId)
            ->orderBy('id', 'desc')
            ->paginate(10);
        // Search for summary text
        if ($request->has('search')) {
            $projects->where('name', $request->input('search'));
        }

        return (new ProjectCollection($projects))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Post(
     *     path="/user/profiles/{profileId}/projects",
     *     operationId="addProject",
     *     description="Creates a new project for user",
     *     summary="Create a new project for a user",
     *     tags={"Projects"},
     *     @OA\Response(
     *         response=200,
     *         description="Project response",
     *         @OA\JsonContent(ref="#/components/schemas/Project")
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
        $validator = Validator::make($request->all(),  self::validationArray($profileId));

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->messages()
            ], 400);
        }

        $project = Project::create([
            'user_id'                    => Auth::user()->id,
            'profile_id'                 => $profileId,
            'name'                       => $request->name,
            'type'                       => $request->type,
            'is_published'               => $request->isPublished,
            'description'                => $request->description,
            'background_uri'             => $request->file('backgroundUri') ? $request->file('backgroundUri')->hashName(): '',
            'original_background_uri'    => $request->file('originalBackgroundUrl') ? $request->file('originalBackgroundUrl')->hashName(): '',
            'avatar_uri'                 => $request->file('avatarUri') ? $request->file('avatarUri')->hashName(): '',
        ]);


        if(!empty($request->tags) && !empty(json_decode($request->tags))) {
            $tags = json_decode($request->tags);
            $project->tags()->attach($tags);
        }


        if(!empty($request->coCreatorsValue) && !empty(json_decode($request->coCreatorsValue))) {
            $coCreatorsValue = json_decode($request->coCreatorsValue);
            foreach ($coCreatorsValue as $item){
                ProjectCoCreator::create([
                    'project_id' => $project->id,
                    'profile_id' => $item->id
                ]);
            }
        }


        if(!empty($request->newTags) && !empty(json_decode($request->newTags))) {
            $newTags = json_decode($request->newTags);
            foreach ($newTags as $tag) {
                $tag = Tag::create([
                    'user_id'           => Auth::user()->id,
                    'name'              => $tag,
                    'description'       => 'description',
                    'rbg_color_code'    => 'rgb',
                    'created_by'        => Auth::user()->id,
                ]);
                $project->tags()->attach([$tag->id]);
            }
        }


        if ($request->hasFile('backgroundUri')) {
            $image = $request->file('backgroundUri');
            Storage::disk('public')->putFile('projects/projectBackgroundImg/'.$project->id, $image);
        }

        if ($request->hasFile('avatarUri')) {
            $image = $request->file('avatarUri');
            Storage::disk('public')->putFile('projects/projectAvatar/'.$project->id, $image);
        }

        if ($request->hasFile('originalBackgroundUrl')) {
            $image = $request->file('originalBackgroundUrl');
            Storage::disk('public')->putFile('projects/projectOriginalBackgroundImg/'.$project->id, $image);
        }

        return (new ProjectResource($project))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="/user/profiles/{profileId}/projects/{projectId}",
     *     summary="Get a single project",
     *     tags={"Projects"},
     *     description="Returns a single project",
     *     operationId="showProject",
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
     *     @OA\Response(
     *         response=200,
     *         description="Project response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Project")
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

    public function show($projectId)
    {
//        if($profileId != "null"){
//            $project = Project::where('profile_id', $profileId)
//                ->where('id', $projectId)->first();
//        }else{
//            $project = Project::where('id', $projectId)->first();
//        }
        $project = Project::where('id', $projectId)->first();

        if($project){
            return (new ProjectResource($project))
                ->response()
                ->setStatusCode(200);
        }

    }

    /**
     * @OA\Put(
     *     path="/user/profiles/{profileId}/projects/{projectId}",
     *     operationId="editProject",
     *     description="Edits a project",
     *     summary="Edit a project",
     *     tags={"Projects"},
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
     *     @OA\RequestBody(
     *         description="User to edit",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/Project")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Event response",
     *         @OA\JsonContent(ref="#/components/schemas/Project")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function update(Request $request, $profileId,  $projectId)
    {

        $getProject = Project::where('profile_id', $profileId)->where('name', $request->name)->get();

        if($getProject != null && count($getProject->toArray()) == 1 && $getProject['0']['id'] != (int)$projectId){
            return response()->json(
                [
                    'error' => ['name' => ['The name has already been taken.']]
                ], 400);
        }

        /** @var User $user */
        $user = Auth::user();

        /** @var Project $project */
        $project = $user->projects()
            ->where('id', $projectId)
            ->first();

        $existName = $user->projects()
            ->where('id', '!=', $project->getKey())
            ->where('profile_id', $profileId)
            ->where('name', $request->name)
            ->count();

        if($existName && $request->oldProfileId != $profileId){
            return response()->json([
                'error' => ['name_unique' =>   ['The name has already been taken']]
            ], 400);
        }

        $project->update([
            'user_id'               => $user->getKey(),
            'profile_id'            => $profileId,
            'name'                  => $request->name,
            'type'                  => $request->type,
            'is_published'          => $request->isPublished,
            'description'           => $request->description,
        ]);


        if(!empty($request->tags) && !empty(json_decode($request->tags))) {
            $tags = json_decode($request->tags);
            $project->tags()->sync($tags);
        }elseif(isset($request->tags) && empty($request->tags)){
            $project->tags()->detache();
        }

        if(!empty($request->newTags) && !empty(json_decode($request->newTags))) {
            $newTags = json_decode($request->newTags);
            foreach ($newTags as $tag) {
                $tag = Tag::create([
                    'user_id'           => $user->getKey(),
                    'name'              => $tag,
                    'description'       => 'description',
                    'rbg_color_code'    => 'rgb',
                    'created_by'        => $user->getKey(),
                ]);
                $project->tags()->attach([$tag->id]);
            }
        }


        if(!empty($request->coCreatorsValue) && !empty(json_decode($request->coCreatorsValue))) {
            ProjectCoCreator::where('project_id', $projectId)->delete();
            $coCreatorsValue = json_decode($request->coCreatorsValue);
            foreach ($coCreatorsValue as $item){
                ProjectCoCreator::create([
                    'project_id' => $project->id,
                    'profile_id' => $item->id
                ]);
            }
        }else{
            ProjectCoCreator::where('project_id', $projectId)->delete();
        }



        if(!empty(json_decode($request->textIds))) {
            $textIds =  json_decode($request->textIds);
            $project->media()->whereIN('media.id',  $textIds)->delete();
        }

        if(!empty(json_decode($request->updateTextIds))) {
            $texts =  json_decode($request->updateTextIds);
            foreach ($texts as $text) {
                $project->media()->where('media.id',  $text->id)->update(['uri'=> $text->text]);
            }
        }

        if(!empty(json_decode($request->updateLinks))) {
            $links =  json_decode($request->updateLinks);
            foreach ($links as $link) {
                $project->media()->where('media.id',  $link->id)->update(
                    ['uri'=> $link->link, 'display_name' => $link->description]
                );
            }
        }

        if ($request->hasFile('backgroundUri')) {
            $image = $request->file('backgroundUri');

            if($project->background_uri){
                Storage::disk('public')->delete('projects/projectBackgroundImg/'.$project->id. '/'.$project->background_uri);
            }

            Storage::disk('public')->putFile('projects/projectBackgroundImg/'.$project->id, $image);

            Project::where('id', $projectId)->update(['background_uri' =>  $image->hashName()]);
        }


        if ($request->hasFile('avatarUri')) {
            $image = $request->file('avatarUri');

            if($project->avatar_uri){
                Storage::disk('public')->delete('projects/projectAvatar/'.$project->id. '/'.$project->avatar_uri);
            }

            Storage::disk('public')->putFile('projects/projectAvatar/'.$project->id, $image);

            Project::where('id', $projectId)->update(['avatar_uri' =>  $image->hashName()]);
        }


        if ($request->hasFile('originalBackgroundUri')) {
            $image = $request->file('originalBackgroundUri');

            if($project->original_background_uri){
                Storage::disk('public')->delete('projects/projectOriginalBackgroundImg/'.$project->id. '/'.$project->original_background_uri);
            }

            Storage::disk('public')->putFile('projects/projectOriginalBackgroundImg/'.$project->id, $image);

            Project::where('id', $projectId)->update(['original_background_uri' =>  $image->hashName()]);
        }

        return (new ProjectResource($project))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Delete(
     *     path="/user/profiles/{profileId}/projects/{projectId}",
     *     description="Delete a single Project based on the  projectId",
     *     operationId="deleteProject",
     *     summary="Delete a project",
     *     tags={"Projects"},
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
     *     @OA\Response(
     *         response=200,
     *         description="Project response",
     *         @OA\JsonContent(ref="#/components/schemas/Project")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function destroy($profileId, $projectId)
    {
        $project = Project::where('user_id', Auth::user()->id)
            ->where('id', $projectId)
            ->where('profile_id', $profileId)
            ->first();

        Teamable::where('teamable_id', '=', $projectId)->delete();

        ProjectCoCreator::where('project_id', '=', $projectId)->delete();

        $project->media()->delete();

        Mediable::where('mediable_id', $projectId)
            ->where('mediable_type', '=', 'App\Project')
            ->delete();

        $project->likes()->delete();

        Likeable::where('likeable_id', $projectId)
            ->where('likeable_type', '=', 'App\Project')
            ->delete();

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

        return (new ProjectResource($project))
            ->response()
            ->setStatusCode(200);
    }


    public function getUserProjectSubscription()
    {
        /** @var User $user */
        $user = Auth::user();

        $subscriptions = $user->subscriptions()->orderBy('id', 'desc')->get();
        $subscriptionProject = 0;
        if($subscriptions && !empty($subscriptions)){
            foreach ($subscriptions  as $subscription){
                if($subscription){
                    if($subscription->plan_id == 1){
                        $subscriptionProject += 1;
                    }elseif ($subscription->plan_id == 2){
                        $subscriptionProject += 1;
                    }elseif ($subscription->plan_id == 3){
                        $subscriptionProject += 10;
                    }
                }
            }
        }else{
            return response()->json([
                'subscription' => false,
                'newProfile' => false,
            ]);
        }

        $projects = $user->projects()->count();

        if($projects < $subscriptionProject ){
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


    public function getAuthUserProjects()
    {
        $projects = Project::where('user_id', Auth::user()->id)
            ->orderBy('id', 'desc')
            ->get();

        return (new ProjectCollection($projects))
            ->response()
            ->setStatusCode(200);
    }


    public function getCoCreators()
    {
        $profileIds = Connect::where('user_id', Auth::user()->id)->pluck('profile_id');

        $profiles = Profile::whereIn('id', $profileIds)->get();

        return (new ProfileCollection($profiles))
            ->response()
            ->setStatusCode(200);
    }

    public function getProjectCoCreators($id)
    {
        $profileIds = ProjectCoCreator::where('project_id', $id)->pluck('profile_id');

        $profiles = Profile::whereIn('id', $profileIds)->get();

        return (new ProfileCollection($profiles))
            ->response()
            ->setStatusCode(200);
    }


    public function getUserProjects($userId)
    {
        $projects = Project::where('user_id', Auth::user()->id)->get();

        return (new ProjectCollection($projects))
            ->response()
            ->setStatusCode(200);
    }


    public function updateProjectColumn($projecId)
    {
        $project = Project::where('id', $projecId)->first();
        Project::where('id', $projecId)->update(['updated' => 0, 'updated_color' => 0]);
        $collecrtions = $project->collections()->get();
        foreach ($collecrtions as $collecrtion){
            Collection::where('id', $collecrtion->id)->update(['updated' => 0]);
        }



    }


    public function getProjects(Request $request )
    {
        $projects = Project::where('user_id', Auth::user()->id)
            ->orderBy('id', 'desc');

        $result = [
            'labels' => [],
            'datasets' => array(
                [
                    'label' => 'Likes',
                    'backgroundColor' => '#d7d7d7',
                    'pointBackgroundColor' => 'white',
                    'borderWidth' => 1,
                    'pointBorderColor' => '#249EBF',
                    'data' => [],
                    'ids' => []
                ],
            )
        ];


        $newProject  = Project::with('likes')
            ->where('user_id', Auth::user()->id)
            ->get();

        foreach ($newProject as $project) {
            array_push($result['labels'], $project->name);
            array_push($result['datasets'][0]['data'], count($project->likes));
            array_push($result['datasets'][0]['ids'], $project->id);
        }

        return response([
            'projects' => new ProjectCollection($projects->get()),
            'projectsHorizontalBar' => $result,
        ])->setStatusCode(200);
    }




    public function getOneProject(Request $request, $projectId, $filter)
    {

        $result = [
            'labels' => ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            'datasets' => array(
                [
                    'label' => 'Likes',
                    'backgroundColor' => '#800080',
                    'pointBackgroundColor' => 'white',
                    'borderWidth' => 1,
                    'pointBorderColor' => '#249EBF',
                    'data' => [],
                ],

                [
                    'label' => 'Views 0',
                    'backgroundColor' => '#99ccff',
                    'pointBackgroundColor' => 'white',
                    'borderWidth' => 1,
                    'pointBorderColor' => '#249EBF',
                    'data' => [0,0,0,0,0,0,0,0,0,0,0,0],
                ]
            )
        ];

        if($filter == 1) {
            $project = Project::selectRaw('COUNT(*) as count, YEAR(likes.liked_date) year, MONTH(likes.liked_date) month')
                ->join('likeables', 'likeables.likeable_id', '=', 'projects.id' )
                ->join('likes', 'likes.id', '=', 'likeables.like_id' )
                ->where('projects.id', $projectId)
                ->where('likeables.likeable_type', 'App\Project')
                ->groupBy('year', 'month')
                ->get();


            $month = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0, 12 => 0];
            foreach ($month as $keyM => $valueM) {
                foreach ($project as $key => $value){
                    if($keyM == $value->month){
                        $month[$keyM] = $value->count;
                    }
                }
            }

            $likesCount = 0;
            foreach($month as $value) {
                array_push($result['datasets'][0]['data'], $value);
                $likesCount += $value;
            }

            $result['datasets'][0]['label'] = 'Likes '. $likesCount;

        }elseif ($filter == 2){

            $firstDayOfPreviousMonth = Carbon::now()->startOfMonth()->toDateString();
            $lastDayOfPreviousMonth = Carbon::now()->endOfMonth()->toDateString();
            $project = Project::selectRaw('COUNT(*) as count, WEEKDAY(likes.liked_date) week,
            MONTH(likes.liked_date) month,  DAY(likes.liked_date) day, likes.liked_date')
                ->join('likeables', 'likeables.likeable_id', '=', 'projects.id' )
                ->join('likes', 'likes.id', '=', 'likeables.like_id' )
                ->where('projects.id', $projectId)
                ->where('likeables.likeable_type', 'App\Project')
                ->whereBetween('likes.liked_date',[$firstDayOfPreviousMonth,$lastDayOfPreviousMonth])
                ->groupBy('week', 'month', 'day', 'likes.liked_date')
                ->get();

            $period = CarbonPeriod::create($firstDayOfPreviousMonth, $lastDayOfPreviousMonth);

            $arrayData = [];
            foreach ($period as $date) {
                $dayOfweek = date('w', strtotime($date));
                if($dayOfweek == 1){
                    array_push($arrayData, $date);
                }
            }

            $result['labels'] = [];
            foreach ($arrayData as $item){
                array_push($result['labels'], $item->format('dS F, Y'));
            }

            foreach ($arrayData as $keyM => $valueM) {
                foreach ($project as $key => $value){
                    if((strtotime($value->liked_date) > strtotime($valueM)) && (strtotime($value->liked_date) < strtotime($valueM->addDays(6)))){

                        array_push($result['datasets'][0]['data'], $value->count);

                    }else{
                        array_push($result['datasets'][0]['data'], 0);
                    }
                }

            }

        }elseif ($filter == 3){

            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek =  Carbon::now()->startOfWeek()->addDay(6);

            $project = Project::selectRaw('COUNT(*) as count,  WEEKDAY(likes.liked_date) week')
                ->join('likeables', 'likeables.likeable_id', '=', 'projects.id' )
                ->join('likes', 'likes.id', '=', 'likeables.like_id' )
                ->where('projects.id', $projectId)
                ->where('likeables.likeable_type', 'App\Project')
                ->whereBetween('likes.liked_date',[$startOfWeek ,$endOfWeek])
                ->groupBy('week')
                ->get();

            $weeks = [0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0];

            foreach ($weeks as $keyM => $valueM) {
                foreach ($project as $key => $value){
                    if($keyM == $value->week){
                        $weeks[$keyM] = $value->count;
                    }
                }
            }

            $result['labels'] = [];

            array_push($result['labels'],
                'Monday',
                'Tuesday',
                'Wednesday',
                'Thursday',
                'Friday',
                'Saturday',
                'Sunday'
            );


            foreach($weeks as $value) {
                array_push($result['datasets'][0]['data'], $value);
            }

        }

        return response($result)
            ->setStatusCode(200);
    }


    public function updateShareCount(Request $request, $projectId)
    {

        Project::where('user_id', Auth::user()->id)
            ->where('id', $projectId)
            ->update(['share_count' => $request->shareCount]);

        $project = Project::where('id', $projectId)->first();
        return (new ProjectResource($project))
            ->response()
            ->setStatusCode(200);
    }


    public function pinToTop(Request $request)
    {
       Project::where('id', $request->id)->update(['pin_to_top' => 1]);
    }

    /*
    * Validation rules for saving to table
    */
    public static function validationArray($profileId):array {
        $validation = array (
            'name' => 'required|max:255|unique:projects,name,NULL,id,profile_id,'.$profileId,
            'type' => 'required|integer',
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
