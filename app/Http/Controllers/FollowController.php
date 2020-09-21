<?php

namespace App\Http\Controllers;

use App\FollowUser;
use App\Http\Helpers;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Profile;
use App\SocialMediaLink;
use App\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use function GuzzleHttp\Promise\all;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Follow;
use Illuminate\Support\Facades\Storage;
use Analytics;
use Spatie\Analytics\Period;

class FollowController extends Controller
{


    /**
     * @OA\Get(
     *     path="/user/follows",
     *     summary="Get a list of follows",
     *     tags={"Follows"},
     *     description="Returns all follow from the system that the user has access to.",
     *     operationId="findFollow",
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
     *         description="Follow response",
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
    public function index()
    {
        $user = User::where('id', Auth::user()->id)->first();
        $follows = $user->follows()->get();
        return (new UserCollection($follows))
            ->response()
            ->setStatusCode(200);
    }


    /**
     * @OA\Post(
     *     path="/user/follows/{followId}",
     *     operationId="addFollow",
     *     description="Creates a new follow for user",
     *     summary="Create a new follow for a user",
     *     tags={"Follows"},
     *     @OA\Response(
     *         response=200,
     *         description="Follow response",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function store(Request $request, $followId)
    {

        $validator = Validator::make(['id' => $followId],  self::validationArray());

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->messages()
            ], 400);
        }

        $follow = FollowUser::create([
            'user_id'            => Auth::user()->id,
            'follow_by_user_id'  => $followId,
            'follow_date'        => Carbon::now()->toDateTimeString(),
        ]);

        $user = User::findOrFail($follow->user_id);

        $profile = Profile::find($follow->follow_by_user_id);

        Profile::where('id', $profile->id)->update(['updated' => 1, 'hide' => 0,  'updated_at' => Carbon::now()->toDateTimeString()]);

        $notificationId = Helpers::storeNotification($profile->user_id, 'follow', 'user', $user->id, $profile->id, $profile->creative_title);

        event(new \App\Events\NewNotification($notificationId, $profile->user_id));

        return (new UserResource($user))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="/user/follows/{followId}",
     *     summary="Get a single follow",
     *     tags={"Follows"},
     *     description="Returns a single follow",
     *     operationId="showFollow",
     *     @OA\Parameter(
     *         name="followId",
     *         in="path",
     *         description="id of the follow",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Follow response",
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

    public function show($followId)
    {
        $follow = User::where('id', $followId)->first();
        return (new UserResource($follow))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Delete(
     *     path="/user/follows/{followId}",
     *     description="Delete a single follow based on the  followId",
     *     operationId="deleteFollow",
     *     summary="Delete a follow",
     *     tags={"Follows"},
     *     @OA\Parameter(
     *         description="followId",
     *         in="path",
     *         name="followId",
     *         required=true,
     *         @OA\Schema(
     *             format="int64",
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Follow response",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function destroy($followId)
    {
        $follow = FollowUser::where('follow_by_user_id', $followId)
            ->where('user_id', Auth::user()->id)
            ->first();

        $user = User::where('id', $follow->user_id)->first();

        $profile = Profile::find($follow->follow_by_user_id);

        Profile::where('id', $profile->id)->update(['updated' => 0,  'updated_at' => Carbon::now()->toDateTimeString()]);

        $follow->delete();

        return (new UserResource($user))
            ->response()
            ->setStatusCode(200);
    }


    public function getFollowers(Request $request)
    {
        $analyticsData = Analytics::fetchVisitorsAndPageViews(Period::days(30));

        $profileView = 0;
        if(!empty($analyticsData)){
            foreach ($analyticsData as $item){
                $profileView +=  $item['pageViews'];
            }
        }

        $followers = FollowUser::select('users.id as user_id','users.first_name as firstName',
            'users.last_name as lastName', 'users.avatar_uri as avatarUri', 'users.location',
            'users.date_of_birth as dateOfBirth', 'content_preference_written as written',
            'content_preference_audio as audio', 'content_preference_visual as visual',
            'content_preference_events as events')
            ->join('profiles', 'follow_users.follow_by_user_id', '=', 'profiles.id')
            ->join('users', 'users.id', '=', 'follow_users.user_id' )
            ->where('profiles.user_id', '=', Auth::user()->id)
            ->get();

        $markers = [];
        foreach ($followers as $item){
            try{
                $geocode_stats = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address='$item->location'&sensor=false&key=AIzaSyCvE9xEo7_ctK8R9n8VCuz3ghWsiqRE8o4");

                $output_deals = json_decode($geocode_stats);

                if(!empty($output_deals->results)){
                    $latLng = $output_deals->results[0]->geometry->location;

                    $lat = $latLng->lat;
                    $lng = $latLng->lng;

                    $markers[] = [
                        'lat' => $lat,
                        'lng' => $lng
                    ];
                }

            }catch (\Exception $e){

            }

        }

        $ids = [];

        $writtenInterests = 0;
        $audioInterests   = 0;
        $visualInterests  = 0;
        $eventsInterests  = 0;

        $age1 = 0;
        $age2 = 0;
        $age3 = 0;
        $age4 = 0;
        $age5 = 0;
        $age6 = 0;

        foreach ($followers as $follower) {
            array_push($ids, $follower->user_id);
            $age = Carbon::parse($follower->dateOfBirth)->age;

            if($follower->written == 1){
                $writtenInterests++;
            }

            if($follower->audio == 1){
                $audioInterests++;
            }

            if($follower->visual == 1){
                $visualInterests++;
            }

            if($follower->events == 1){
                $eventsInterests++;
            }

            if($age >= 8 &&  $age <= 14){
                $age1++;
            }

            if($age >= 14 &&  $age <= 22){
                $age2++;
            }

            if($age >= 22 &&  $age <= 30){
                $age3++;
            }

            if($age >= 30 &&  $age <= 40){
                $age4++;
            }

            if($age >= 40 &&  $age <= 50){
                $age5++;
            }

            if($age >= 50 &&  $age <= 60){
                $age6++;
            }

        }

        $ages = [$age1, $age2, $age3, $age4, $age5, $age6];

        $interests = [$writtenInterests, $audioInterests, $visualInterests, $eventsInterests];

        $socials = SocialMediaLink::whereIn('user_id', $ids)->get();

        $tumblr = 0;
        $twitter = 0;
        $soundcloud = 0;
        $spotify = 0;
        $youtube = 0;
        $vimeo = 0;
        $behance = 0;
        $linkedin = 0;
        $etsy = 0;
        $facebook = 0;
        $instagram = 0;
        $snapchat = 0;

       foreach ($socials as $social) {
           if($social->name == 'tumblr') {
               $tumblr++;
           }

           if($social->name == 'twitter') {
               $twitter++;
           }

           if($social->soundcloud == 'soundcloud') {
               $soundcloud++;
           }

           if($social->spotify == 'spotify') {
               $spotify++;
           }

           if($social->youtube == 'youtube') {
               $youtube++;
           }

           if($social->vimeo == 'vimeo') {
               $vimeo++;
           }

           if($social->behance == 'behance') {
               $behance++;
           }

           if($social->linkedin == 'linkedin') {
               $linkedin++;
           }

           if($social->etsy == 'etsy') {
               $etsy++;
           }

           if($social->facebook == 'facebook') {
               $facebook++;
           }

           if($social->instagram == 'instagram') {
               $instagram++;
           }

           if($social->snapchat == 'snapchat') {
               $snapchat++;
           }
       }

        $resultDataCollectionSocial = [ $tumblr, $twitter, $soundcloud, $spotify, $youtube, $vimeo, $behance, $linkedin, $etsy, $facebook, $instagram, $snapchat];

        $result = [
            'labels' => [],
            'datasets' => array(
                [
                    'label' => 'Followers',
                    'backgroundColor' => '#800080',
                    'pointBackgroundColor' => 'white',
                    'borderWidth' => 1,
                    'pointBorderColor' => '#249EBF',
                    'data' => [],
                ],

                [
                    'label' => 'Visits to the profile',
                    'backgroundColor' => '#99ccff',
                    'pointBackgroundColor' => 'white',
                    'borderWidth' => 1,
                    'pointBorderColor' => '#249EBF',
                    'data' => [$profileView,0,0,0,0,0,0,0,0,0,0,0],
                ]
            )
        ];


       if($request->filter == 1 || $request->filter == 'undefined'){
           $followersCount = FollowUser::selectRaw('COUNT(*) as count, YEAR(follow_users.follow_date) year, 
        MONTH(follow_users.follow_date) month')
               ->join('profiles', 'follow_users.follow_by_user_id', '=', 'profiles.id' )
               ->join('users', 'users.id', '=', 'follow_users.user_id' )
               ->where('profiles.user_id', '=', Auth::user()->id)
               ->groupBy('year', 'month')
               ->get();

           $month = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0, 12 => 0];
           foreach ($month as $keyM => $valueM) {
               foreach ($followersCount as $key => $value){
                   if($keyM == $value->month){
                       $month[$keyM] = $value->count;
                   }
               }
           }

           array_push($result['labels'],
               'January',
                 'February',
                    'March',
                    'April',
                    'May',
                    'June',
                    'July',
                    'August',
                    'September',
                    'October',
                    'November',
                    'December'
           );

           foreach($month as $value) {
               array_push($result['datasets'][0]['data'], $value);
           }
       }elseif ($request->filter == 2){

           $firstDayOfPreviousMonth = Carbon::now()->startOfMonth()->toDateString();
           $lastDayOfPreviousMonth = Carbon::now()->endOfMonth()->toDateString();
           $followersCountWeek = FollowUser::selectRaw('COUNT(*) as count, WEEKDAY(follow_users.follow_date) week, 
        MONTH(follow_users.follow_date) month, DAY(follow_users.follow_date) day, follow_users.follow_date' )
               ->join('profiles', 'follow_users.follow_by_user_id', '=', 'profiles.id' )
               ->join('users', 'users.id', '=', 'follow_users.user_id' )
               ->where('profiles.user_id', '=', Auth::user()->id)
               ->whereBetween('follow_users.follow_date',[$firstDayOfPreviousMonth,$lastDayOfPreviousMonth])
               ->groupBy('week', 'month', 'day', 'follow_users.follow_date')
               ->get();



           $period = CarbonPeriod::create($firstDayOfPreviousMonth, $lastDayOfPreviousMonth);


           $arrayData = [];

           foreach ($period as $date) {
               $dayOfweek = date('w', strtotime($date));
               if($dayOfweek == 1){
                   array_push($arrayData, $date);
               }


           }


           foreach ($arrayData as $item){
               array_push($result['labels'], $item->format('dS F, Y'));
           }

           foreach ($arrayData as $keyM => $valueM) {
               foreach ($followersCountWeek as $key => $value){
                   if((strtotime($value->follow_date) > strtotime($valueM)) && (strtotime($value->follow_date) < strtotime($valueM->addDays(6)))){

                       array_push($result['datasets'][0]['data'], $value->count);

                   }else{
                       array_push($result['datasets'][0]['data'], 0);
                   }
               }

           }


       }elseif ($request->filter == 3) {

           $startOfWeek = Carbon::now()->startOfWeek();
           $endOfWeek = Carbon::now()->startOfWeek()->addDay(6);

           $followersCountByDay = FollowUser::selectRaw('COUNT(*) as count, 
                WEEKDAY(follow_users.follow_date) week')
               ->join('profiles', 'follow_users.follow_by_user_id', '=', 'profiles.id' )
               ->join('users', 'users.id', '=', 'follow_users.user_id' )
               ->where('profiles.user_id', '=', Auth::user()->id)
               ->whereBetween('follow_users.follow_date',[$startOfWeek ,$endOfWeek])
               ->groupBy('week')
               ->get();


           $weeks = [0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0];

           foreach ($weeks as $keyM => $valueM) {
               foreach ($followersCountByDay as $key => $value){
                   if($keyM == $value->week){
                       $weeks[$keyM] = $value->count;
                   }
               }
           }

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

        return response([
            'followersCount' => $result,
            'followers' => $followers,
            'resultDataCollectionSocial' => $resultDataCollectionSocial,
            'interests'        => $interests,
            'ages'             => $ages,
            'profileView'      => $profileView,
            'markers'          => $markers,
        ])->setStatusCode(200);
    }

    /*
    * Validation rules for saving to table
    */
    public static function validationArray():array {
        $validation = array (
            'id' => 'required|numeric',
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
