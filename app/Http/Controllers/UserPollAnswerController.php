<?php

namespace App\Http\Controllers;


use App\Http\Resources\PollCollection;
use App\Http\Resources\PollResource;
use App\Http\Resources\UserPollAnswerResource;
use App\Poll;
use App\UserPollAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;


class UserPollAnswerController extends Controller
{

    public function store(Request $request, $pollId)
    {
        $validator = Validator::make($request->all(),  self::validationArray());
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->messages()
            ], 400);
        }

        UserPollAnswer::create([
            "user_id"                               => Auth::user()->id,
            "poll_id"                               => $pollId,
            "select_answer_id"                      => $request->selectAnswerId,
            "open_answer"                           => $request->openAnswer,
            "answer_type"                           => $request->answerType,
        ]);

        $poll = Poll::where('user_id', Auth::user()->id)->get();
        return (new PollCollection($poll))
            ->response()
            ->setStatusCode(201);
    }



    /*
     * Validation rules for saving to table
     */
    public static function validationArray():array {

        $validation = array (
            "answerType" => 'required|integer',
        );

        return $validation;
    }


    /*
     * Validation rules for updating to table
     */
    public static function validationUpdateArray($pollId):array {
        $validation = array (
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
