<?php

namespace App\Http\Controllers;

use App\Http\Helpers;
use App\Http\Resources\MessageCollection;
use App\Http\Resources\MessageResource;
use App\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class MessageController extends Controller
{
    /**
     * @OA\Get(
     *     path="/user/messages",
     *     summary="Get a list of messages for user",
     *     tags={"Messages"},
     *     description="Returns all messages from the system that the user has access to.",
     *     operationId="findMessages",
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="filter by message text",
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
     *         description="Message response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Message")
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
    public function index(Request $request, $toUserId)
    {
        $fromUserId =  Auth::user()->id;
        $messages = Message::whereRaw('(from_user_id = "'.$fromUserId.'" and to_user_id = "'.$toUserId.'" )
            or (from_user_id = "'.$toUserId.'"  and  to_user_id = "'.$fromUserId.'")')->get();

        return (new MessageCollection($messages))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Post(
     *     path="/user/messages",
     *     operationId="addMessage",
     *     description="Creates a new message for user",
     *     summary="Create a new message for a user",
     *     tags={"Messages"},
     *     @OA\Response(
     *         response=200,
     *         description="Message response",
     *         @OA\JsonContent(ref="#/components/schemas/Message")
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


        $message = Message::create([
            'from_user_id'      => Auth::user()->id,
            'to_user_id'        => $request->toUserId,
            'message'           => $request->message,
            'summary'           => $request->summary,
            'is_read'           => $request->isRead
        ]);

        $notificationId = Helpers::storeNotification($message->to_user_id, 'message', 'message', $message->id, null, $message->summary);

        event(new \App\Events\NewNotification($notificationId, $message->to_user_id));

        event(new \App\Events\NewMessage($message->id, $message->to_user_id));

        return (new MessageResource($message))
            ->response()
            ->setStatusCode(201);
    }


    /**
     * @OA\Get(
     *     path="/user/messages/{messageId}/show",
     *     summary="Get a single message",
     *     tags={"Messages"},
     *     description="Returns a single message",
     *     operationId="showMessage",
     *     @OA\Parameter(
     *         name="messageId",
     *         in="path",
     *         description="id of the message",
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
     *             @OA\Items(ref="#/components/schemas/Message")
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

    public function show($messageId)
    {
        $message = Message::where('id', $messageId)->first();

        return (new MessageResource($message))
            ->response()
            ->setStatusCode(200);
    }


    /**
     * @OA\Put(
     *     path="user/messages/{messageId}",
     *     operationId="editMessage",
     *     description="Edit a message",
     *     summary="Edit a message",
     *     tags={"Messages"},
     *     @OA\Parameter(
     *         name="messageId",
     *         in="path",
     *         description="id of the message",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Message to edit",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/Message")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Message response",
     *         @OA\JsonContent(ref="#/components/schemas/Message")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function update(Request $request, $messageId)
    {
        $validator = Validator::make($request->all(), self::validationStoreArray());

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->messages()
            ], 400);
        }

        $message =  Message::where('id', $messageId)->first();

        $message->update([
            'from_user_id'      => Auth::user()->id,
            'to_user_id'        => $request->toUserId,
            'message'           => $request->message,
            'summary'           => $request->summary,
            'is_read'           => $request->isRead
        ]);

        return (new MessageResource($message))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Delete(
     *     path="/user/messages/{messageId}",
     *     description="Delete a single Message based on the  messageId",
     *     operationId="deleteMessage",
     *     summary="Delete a message",
     *     tags={"Messages"},
     *     @OA\Parameter(
     *         description="messageId",
     *         in="path",
     *         name="messageId",
     *         required=true,
     *         @OA\Schema(
     *             format="int64",
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Message response",
     *         @OA\JsonContent(ref="#/components/schemas/Message")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function destroy($messageId)
    {
        $message = Message::where('id', $messageId)->first();
        $message->delete();

        return (new MessageResource($message))
            ->response()
            ->setStatusCode(200);
    }


    /*
   * Validation rules for saving to table
   */
    public static function validationStoreArray():array {
        $validation = array (
            'toUserId'   => 'required|numeric',
            'message'   =>    'required|string|max:191',
        );
        return $validation;
    }


    /*
     * Validation messages
     */
    public static function validationMessages():array {
        $validationMessages = array (
            'message.required' => 'This message field is required',
        );
        return $validationMessages;
    }
}
