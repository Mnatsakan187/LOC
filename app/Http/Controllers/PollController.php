<?php

namespace App\Http\Controllers;

use App\Answer;
use App\Http\Resources\PollCollection;
use App\Http\Resources\PollResource;
use App\Likeable;
use App\Poll;
use App\UserPollAnswer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\Storage;

class PollController extends Controller
{
    /**
     * @OA\Get(
     *     path="/user/profiles/{profileId}/polls",
     *     summary="Get a list of polls",
     *     tags={"Poll"},
     *     description="Returns all  polls from the system that the user has access to.",
     *     operationId="findPoll",
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
     *         description="Poll response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Poll")
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
        $poll = Poll::where('profile_id', $profileId)
            ->paginate(10);

        // Search for summary text
        if ($request->has('search')) {
            $poll->where('name', $request->input('search'));
        }

        return ( new PollCollection($poll))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Post(
     *     path="/user/profiles/{profileId}/polls",
     *     operationId="addPolls",
     *     description="Creates a new project for user",
     *     summary="Create a new project for a user",
     *     tags={"polls"},
     *     @OA\Response(
     *         response=200,
     *         description="Polls response",
     *         @OA\JsonContent(ref="#/components/schemas/Poll")
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


        $poll = Poll::create([
            "user_id"                               => Auth::user()->id,
            "profile_id"                            => isset($request->profile) && !empty($request->profile) ? $request->profile['id'] : null,
            "project_id"                            => isset($request->projectId) && !empty($request->projectId) ? $request->projectId['id'] : null,
            "name"                                  => $request->name,
            "question"                              => $request->question,
            "answer_type"                           => $request->answerType,
            "answer"                                => $request->answer,
        ]);

        if($request->answerType == 2) {
            $arr = [];
            foreach ($request->answers as $key => $value){
                if($value['value'] != ''){
                    $arr[] = [
                        'poll_id'     => $poll->id,
                        'answer'      => $value['value'],
                        'created_at'  => Carbon::now()->toDateTimeString(),
                        'updated_at'  => Carbon::now()->toDateTimeString(),
                    ];
                }
            }

            Answer::insert($arr);
        }

        return (new PollResource($poll))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="/user/profiles/{profileId}/polls/{pollId}",
     *     summary="Get a single poll",
     *     tags={"Poll"},
     *     description="Returns a single poll",
     *     operationId="showPoll",
     *     @OA\Parameter(
     *         name="pollId",
     *         in="path",
     *         description="id of the poll",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Poll response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Poll")
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
    public function show($pollId)
    {
        $poll = Poll::where('id', $pollId)->first();

        return ( new PollResource($poll))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Put(
     *     path="/user/profiles/{profileId}/polls/{pollId}",
     *     operationId="editPoll",
     *     description="Edits a poll",
     *     summary="Edit a poll",
     *     tags={"Poll"},
     *     @OA\Parameter(
     *         name="pollId",
     *         in="path",
     *         description="id of the pollId",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Poll to edit",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/Poll")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Poll response",
     *         @OA\JsonContent(ref="#/components/schemas/Poll")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function update(Request $request, $pollId)
    {
        $validator = Validator::make($request->all(), self::validationUpdateArray($pollId));

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->messages()
            ], 400);
        }


        $poll = Poll::where('user_id', Auth::user()->id)->where('id', $pollId)
            ->first();

        $poll->update([
            "user_id"                               => Auth::user()->id,
            "profile_id"                            => isset($request->profile) && !empty($request->profile) ? $request->profile['id'] : null,
            "project_id"                            => isset($request->projectId) && !empty($request->projectId) ? $request->projectId['id']: null,
            "name"                                  => $request->name,
            "question"                              => $request->question,
            "answer_type"                           => $request->answerType,
            "answer"                                => $request->answer,
        ]);


        if($request->answerType == 2) {
            Answer::where('poll_id', $poll->id)->delete();
            $arr = [];
            foreach ($request->fillAnswers as $key => $value){
                if($value['value'] != ''){
                    $arr[] = [
                        'poll_id'     => $poll->id,
                        'answer'      => $value['value'],
                        'created_at'  => Carbon::now()->toDateTimeString(),
                        'updated_at'  => Carbon::now()->toDateTimeString(),
                    ];
                }
            }
            Answer::insert($arr);
        }else{
            Answer::where('poll_id', $poll->id)->delete();
        }

        return ( new PollResource($poll))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Delete(
     *     path="/user/profiles/{profileId}/polls/{pollId}",
     *     description="Delete a single Poll based on the  pollId",
     *     operationId="deletePoll",
     *     summary="Delete a poll",
     *     tags={"Poll"},
     *     @OA\Parameter(
     *         description="pollId",
     *         in="path",
     *         name="pollId",
     *         required=true,
     *         @OA\Schema(
     *             format="int64",
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Poll response",
     *         @OA\JsonContent(ref="#/components/schemas/Poll")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function destroy($pollId)
    {
        $poll = Poll::where('user_id', Auth::user()->id)->where('id', $pollId)
            ->first();


        $poll->likes()->delete();

        Likeable::where('likeable_id', $pollId)
            ->where('likeable_type', '=', 'App\Poll')
            ->delete();

        $poll->delete();

        return ( new PollResource($poll))
            ->response()
            ->setStatusCode(200);
    }



    public function getPolls()
    {
        $polls  =  Poll::where('user_id', Auth::user()->id)->get();
        return ( new PollCollection($polls))
            ->response()
            ->setStatusCode(200);
    }


    public function getPollDiagram($id)
    {
        $poll = Poll::where('id', $id)->first();

        if($poll->answer_type == 2){

            $result = [
                "labels" => [],
                "datasets" => [
                    [
                        "label" => "Data One",
                        "backgroundColor" => [],
                        "data" => []
                    ]
                ]
            ];

            $answers =  $poll->answers()->get();

            foreach ($answers as $answer) {
                array_push($result['labels'], $answer->answer);
                array_push($result['datasets'][0]['data'], UserPollAnswer::where('select_answer_id', $answer->id)->count());
                array_push($result['datasets'][0]['backgroundColor'], '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT));
            }


        }else{
            $result = UserPollAnswer::with('user')
            ->where('poll_id', $poll->id)->get();

        }



        return response([
            'pollPieChart' => $result,
            'poll'         => $poll
        ])->setStatusCode(200);
    }


    public function pinToTop(Request $request)
    {
        Poll::where('id', $request->id)->update(['pin_to_top' => 1]);
    }

    /*
     * Validation rules for saving to table
     */
    public static function validationArray():array {

        $validation = array (
            "question"   => 'required|max:1000|unique:polls,name,NULL,id,user_id,'.Auth::user()->id,
            "answerType" => 'required|integer',
        );

        return $validation;
    }


    /*
     * Validation rules for updating to table
     */
    public static function validationUpdateArray($pollId):array {
        $validation = array (
            "question"   => 'required|unique:polls,name,NULL,id,user_id,'.$pollId,
            "answerType" => 'required|integer',
        );

        return $validation;
    }

    /*
     * Validation messages
     */
    public static function validationMessages():array {
        $validationMessages = array (
            'question.required' => 'This question field is required',
        );
        return $validationMessages;
    }
}
