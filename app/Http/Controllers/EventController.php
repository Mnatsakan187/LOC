<?php

namespace App\Http\Controllers;

use App\Http\Resources\EventCollection;
use App\Http\Resources\EventResource;
use Carbon\Carbon;
use function GuzzleHttp\Promise\all;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Event;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{

    public function getEvents(Request $request)
    {
        $events = Event::select('start_date as date', 'name as title');
        // Search for summary text
        if ($request->has('search')) {
            $events->where('name', $request->input('search'));
        }

        if($request->has('userEvent') && !empty($request->has('userEvent') &&   $request->userEvent != null)){
            $events =  $events->where('user_id', Auth::user()->id);

        }

        if($request->has('categories') && !empty($request->has('categories') &&   $request->categories != null)){
            $array = explode(',', $request->categories);
            $events =  $events->whereIn('category_id', $array);

        }

        if($request->has('cost') && !empty($request->has('cost') &&   $request->cost != null)){
            if($request->cost == 1) {
                $events =  $events->where('cost', '');
            }elseif ($request->cost == 2){
                $events =  $events->where('cost', '<=', 10);
            }elseif ($request->cost == 3){
                $events =  $events->whereBetween('cost', [11, 20]);
            }elseif ($request->cost == 4){
                $events =  $events->where('cost', '>', 20);
            }

        }

        $events = $events->get();
        return response($events)
            ->setStatusCode(200);
    }


    public function getEventsOnMap(Request $request)
    {
        $events = Event::orderBy('id', 'desc');

        // Search for summary text
        if ($request->has('search')) {
            $events->where('name', $request->input('search'));
        }

        if($request->has('userEvent') && !empty($request->has('userEvent') &&   $request->userEvent != null)){
            $events =  $events->where('user_id', Auth::user()->id);

        }

        if($request->has('categories') && !empty($request->has('categories') &&   $request->categories != null)){;
            $events =  $events->whereIn('category_id', $request->categories);

        }

        if (in_array(1, $request->dateFilter)){
            $events  = $events->where('id', '>', 0);
        } else if (in_array(2, $request->dateFilter)){
            $todayFrom = Carbon::today();
            $todayTo   = Carbon::today()->addHours(23)->addMinutes(59)->addSecond(59);
            $events    =  $events->whereBetween('start_date', [$todayFrom, $todayTo]);
        }elseif (in_array(3, $request->dateFilter)) {
            $tomorrowFrom = Carbon::tomorrow();
            $tomorrowTo = Carbon::tomorrow()->addHours(23)->addMinutes(59)->addSecond(59);
            $events =       $events->whereBetween('start_date', [$tomorrowFrom, $tomorrowTo]);
        }elseif (in_array(4, $request->dateFilter)){
            $weekFrom = Carbon::today();
            $weekTo   =  Carbon::today()->addDay(7)->addHours(23)->addMinutes(59)->addSecond(59);
            $events =       $events->whereBetween('start_date', [$weekFrom, $weekTo]);
        }elseif ($request->fromDate  && $request->toDate){
            $from = Carbon::createFromFormat('Y-m-d H:i:s', $request->fromDate.'00:00:00');
            $to = Carbon::createFromFormat('Y-m-d H:i:s', $request->toDate.'23:59:59');
            $events = $events->whereBetween('start_date', [$from, $to]);
        }


        if($request->has('cost') && !empty($request->has('cost') &&   $request->cost != null)){
            if($request->cost == 1) {
                $events =  $events->where('cost', '');
            }elseif ($request->cost == 2){
                $events =  $events->where('cost', '<=', 10);
            }elseif ($request->cost == 3){
                $events =  $events->whereBetween('cost', [11, 20]);
            }elseif ($request->cost == 4){
                $events =  $events->where('cost', '>', 20);
            }
        }

        return (new EventCollection($events->get()))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Get(
     *     path="/user/events",
     *     summary="Get a list of events",
     *     tags={"Events"},
     *     description="Returns all event from the system that the user has access to.",
     *     operationId="findEvent",
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
     *         description="Event response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Event")
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
        $events = Event::orderBy('id', 'desc')
            ->where('profile_id', $profileId)
            ->paginate(10);

        // Search for summary text
        if ($request->has('search')) {
            $events->where('name', $request->input('search'));
        }

        return (new EventCollection($events))
            ->response()
            ->setStatusCode(200);
    }


    /**
     * @OA\Post(
     *     path="/user/events",
     *     operationId="addEvent",
     *     description="Creates a new event for user",
     *     summary="Create a new event for a user",
     *     tags={"Events"},
     *     @OA\Response(
     *         response=200,
     *         description="Event response",
     *         @OA\JsonContent(ref="#/components/schemas/Event")
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

        $event = Event::create([
            'user_id'            => Auth::user()->id,
            'profile_id'         => $profileId,
            'category_id'        => $request->categoryId,
            'name'               => $request->name,
            'date'               => Carbon::now()->toDateTimeString(),
            'duration_in_hours'  => $request->durationInHours,
            'venue'              => $request->venue,
            'street_adress'      => $request->streetAdress,
            'number'             => $request->number,
            'postal_code'        => $request->postalCode,
            'city'               => $request->city,
            'town'               => $request->town,
            'country'            => $request->country,
            'latitude'           => $request->latitude,
            'longitud'           => $request->longitude,
            'is_published'       => $request->isPublished,
            'cost'               => $request->cost,
            'start_date'         => $request->startDate,
            'end_date'           => $request->endDate,
            'description'        => $request->description,
            'poster_uri'         => $request->file('posterUri') ? $request->file('posterUri')->hashName(): '',
            'background_uri'     => $request->file('backgroundUri') ? $request->file('backgroundUri')->hashName(): '',
        ]);


        if ($request->hasFile('posterUri')) {
            $image = $request->file('posterUri');

            Storage::disk('public')->putFile('events/poster/'.$event->id, $image);
        }

        if ($request->hasFile('backgroundUri')) {
            $image = $request->file('backgroundUri');

            Storage::disk('public')->putFile('events/background/'.$event->id, $image);
        }

        return (new EventResource($event))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="/user/events/{eventId}",
     *     summary="Get a single event",
     *     tags={"Events"},
     *     description="Returns a single event",
     *     operationId="showEvent",
     *     @OA\Parameter(
     *         name="eventId",
     *         in="path",
     *         description="id of the event",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Event response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Event")
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

    public function show($profileId, $eventId)
    {
        $event = Event::where('user_id', Auth::user()->id)
            ->where('id', $eventId)
            ->where('profile_id', $profileId)
            ->first();

        return (new EventResource($event))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Put(
     *     path="/user/events/{eventId}",
     *     operationId="editEvent",
     *     description="Edits a event",
     *     summary="Edit a event",
     *     tags={"Events"},
     *     @OA\Parameter(
     *         name="eventId",
     *         in="path",
     *         description="id of the event",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Event to edit",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/Event")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Event response",
     *         @OA\JsonContent(ref="#/components/schemas/Event")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function update(Request $request, $profileId, $eventId)
    {
        $validator = Validator::make($request->all(), self::validationUpdateArray($eventId));

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->messages()
            ], 400);
        }

        $event = Event::where('user_id', Auth::user()->id)->where('id', $eventId)->first();
        $event->update([
            'user_id'            => Auth::user()->id,
            'profile_id'         => $profileId,
            'category_id'        => $request->categoryId,
            'name'               => $request->name,
            'date'               => Carbon::now()->toDateTimeString(),
            'duration_in_hours'  => $request->durationInHours,
            'venue'              => $request->venue,
            'street_adress'      => $request->streetAdress,
            'number'             => $request->number,
            'postal_code'        => $request->postalCode,
            'city'               => $request->city,
            'town'               => $request->town,
            'country'            => $request->country,
            'latitude'           => $request->latitude,
            'longitud'           => $request->longitude,
            'is_published'       => $request->isPublished,
            'cost'               => $request->cost,
            'start_date'         => $request->startDate,
            'end_date'           => $request->endDate,
            'description'        => $request->description,
        ]);

        if ($request->hasFile('posterUri')) {

            if($event->posterUri){
                Storage::disk('public')->delete('events/poster/'.$event->id. '/'.$event->posterUri);
            }

            $image = $request->file('posterUri');

            Storage::disk('public')->putFile('events/poster/'.$event->id, $image);

            Event::where('id', $eventId)->update(['poster_uri' =>  $image->hashName()]);
        }

        if ($request->hasFile('backgroundUri')) {

            if($event->backgroundUri){
                Storage::disk('public')->delete('events/background/'.$event->id. '/'.$event->backgroundUri);
            }

            $image = $request->file('backgroundUri');

            Storage::disk('public')->putFile('events/background/'.$event->id, $image);

            Event::where('id', $eventId)->update(['background_uri' =>  $image->hashName()]);
        }

        $event = Event::where('id', $eventId)->first();

        return (new EventResource($event))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Delete(
     *     path="/user/events/{eventId}",
     *     description="Delete a single event based on the  eventId",
     *     operationId="deleteEvent",
     *     summary="Delete a event",
     *     tags={"Events"},
     *     @OA\Parameter(
     *         description="eventId",
     *         in="path",
     *         name="eventId",
     *         required=true,
     *         @OA\Schema(
     *             format="int64",
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Event response",
     *         @OA\JsonContent(ref="#/components/schemas/Event")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function destroy($profileId, $eventId)
    {
        $event = Event::where('user_id', Auth::user()->id)
            ->where('id', $eventId)
            ->where('profile_id', $profileId)
            ->first();
        $event->delete();

        return (new EventResource($event))
            ->response()
            ->setStatusCode(200);
    }

    public function events(Request $request )
    {
        $events = Event::where('user_id', Auth::user()->id)
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

        $newEvent  = Event::with('likes')
            ->where('user_id', Auth::user()->id)
            ->get();

        foreach ($newEvent as $event) {
            array_push($result['labels'], $event->name);
            array_push($result['datasets'][0]['data'], count($event->likes));
            array_push($result['datasets'][0]['ids'], $event->id);
        }

        return  response([
            'events' =>  new EventCollection($events->get()),
            'eventsHorizontalBar' => $result
        ])->setStatusCode(200);
    }

    public function getOneEvent(Request $request, $eventId)
    {
        $event = Event::selectRaw('COUNT(*) as count, YEAR(likes.liked_date) year, MONTH(likes.liked_date) month')
            ->join('likeables', 'likeables.likeable_id', '=', 'events.id' )
            ->join('likes', 'likes.id', '=', 'likeables.like_id' )
            ->where('events.id', $eventId)
            ->where('likeables.likeable_type', 'App\Event')
            ->groupBy('year', 'month')
            ->get();

        $month = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0, 12 => 0];
        foreach ($month as $keyM => $valueM) {
            foreach ($event as $key => $value){
                if($keyM == $value->month){
                    $month[$keyM] = $value->count;
                }
            }
        }
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
                    'data' => [1,2,2,5,4,2,9,8,1,3,9,3],
                ]
            )
        ];

        $likesCount = 0;
        foreach($month as $value) {
            array_push($result['datasets'][0]['data'], $value);
            $likesCount += $value;
        }

        $result['datasets'][0]['label'] = 'Likes '. $likesCount;
        return response($result)
            ->setStatusCode(200);
    }

    public function getEventsList(Request $request)
    {


        $events = Event::orderBy('id', 'desc');

        if($request->day == 1) {
            $events =  $events->whereDate('start_date', Carbon::today());
        }elseif ($request->day == 2){
            $events =  $events->whereDate('start_date', Carbon::tomorrow());
        }elseif ($request->day == 3){
           $events =  $events->whereBetween('start_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        }


        if($request->has('userEvent') && !empty($request->has('userEvent') &&   $request->userEvent != null)){
            $events =  $events->where('user_id', Auth::user()->id);

        }

        if($request->has('categories') && !empty($request->has('categories') &&   $request->categories != null)){
            $events =  $events->whereIn('category_id', $request->categories);

        }

        if($request->has('cost') && !empty($request->has('cost') &&   $request->cost != null)){
            if($request->cost == 1) {
                $events =  $events->where('cost', '');
            }elseif ($request->cost == 2){
                $events =  $events->where('cost', '<=', 10);
            }elseif ($request->cost == 3){
                $events =  $events->whereBetween('cost', [11, 20]);
            }elseif ($request->cost == 4){
                $events =  $events->where('cost', '>', 20);
            }


        }

        // Search for summary text
        if(!empty($request->search) && $request->has('search')) {
            $events =   $events->where('name', 'like', '%'. $request->input('search'). '%');
        }

        $events = $events->paginate(10);
        return (new EventCollection($events))
            ->response()
            ->setStatusCode(200);
    }



    /*
    * Validation rules for saving to table
    */
    public static function validationArray():array {
        $validation = array (
            'name' => 'required|max:255|unique:events,name,NULL,id,user_id,'.Auth::user()->id,
        );

        return $validation;
    }


    /*
  * Validation rules for saving to table
  */
    public static function validationUpdateArray($eventId):array {
        $validation = array (
            'name' => 'required|max:255|unique:events,name,NULL,id,user_id,'.$eventId,

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
