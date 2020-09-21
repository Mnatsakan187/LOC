<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReplyCollection;
use App\Http\Resources\ReplyResource;
use App\Reply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class ReplyController extends Controller
{
    /**
     * @OA\Get(
     *     path="user/comments/{commentId}/replies",
     *     summary="Get a list of replies based on comment",
     *     tags={"Reply"},
     *     description="Returns all replies from the system that the user has access to.",
     *     operationId="findReplies",
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="filter by Comment name text",
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
     *         description="Comment response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Reply")
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
    public function index(Request $request, $commentId)
    {
        $reply = Reply::where('user_id', Auth::user()->id)
            ->where('comment_id', $commentId)->first();

        // Search for summary text
        if ($request->has('search')) {
            $reply->where('name', $request->input('search'));
        }

        return (new ReplyCollection($reply))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Post(
     *     path="user/comments/{commentId}/replies",
     *     operationId="addReply  to Comment",
     *     description="Creates a new Reply for Comment",
     *     summary="Create a new Reply for a Comment",
     *     tags={"Reply"},
     *     @OA\Response(
     *         response=200,
     *         description="Comment response",
     *         @OA\JsonContent(ref="#/components/schemas/Reply")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function store(Request $request, $commentId)
    {
        $validator = Validator::make($request->all(),  self::validationArray());

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->messages()
            ], 400);
        }

        $reply = Reply::create([
            'user_id'           => Auth::user()->id,
            'reply_text'        => $request->replyText,
            'comment_id'        => $commentId,
        ]);

        return (new ReplyResource($reply))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="user/comments/{commentId}/replies/{replyId}",
     *     summary="Get a single reply based on the  commentId ",
     *     tags={"Reply"},
     *     description="Returns a single Reply",
     *     operationId="showComment",
     *     @OA\Parameter(
     *         name="commentId",
     *         in="path",
     *         description="id of the comment",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comment response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Reply")
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
    public function show($commentId, $replyId)
    {
        $reply = Reply::where('user_id', Auth::user()->id)
            ->where('id', $replyId)
            ->where('comment_id', $commentId)
            ->first();

        return (new ReplyResource($reply))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Put(
     *     path="user/comments/{commentId}/replies",
     *     operationId="edit Reply based on the Comment",
     *     description="Edits a Reply based on the Comment",
     *     summary="Edit a Reply based on the Comment",
     *     tags={"Reply"},
     *     @OA\Parameter(
     *         name="commentId",
     *         in="path",
     *         description="id of the comment",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Comment to edit",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/Reply")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comment response",
     *         @OA\JsonContent(ref="#/components/schemas/Reply")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function update(Request $request, $commentId, $replyId)
    {
        $validator = Validator::make($request->all(), self::validationArray());

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->messages()
            ], 400);
        }

        $reply = Reply::where('user_id', Auth::user()->id)
            ->where('id', $replyId)
            ->where('comment_id', $commentId)
            ->first();


        $reply ->update([
            'user_id'            => Auth::user()->id,
            'reply_text'         => $request->replyText,
            'comment_id'         => $commentId,
        ]);

        return (new ReplyResource($reply))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Delete(
     *     path="user/comments/{commentId}/replies/{replyId}",
     *     description="Delete a single Reply based on the  comment",
     *     operationId="deleteReply based on the  comment",
     *     summary="Delete a Reply based on the  comment",
     *     tags={"Reply"},
     *     @OA\Parameter(
     *         description="commentId",
     *         in="path",
     *         name="commentId",
     *         required=true,
     *         @OA\Schema(
     *             format="int64",
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comment response",
     *         @OA\JsonContent(ref="#/components/schemas/Reply")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function destroy($commentId, $replyId)
    {
        $reply = Reply::where('user_id', Auth::user()->id)
            ->where('id', $replyId)
            ->where('comment_id', $commentId)->first();

        $reply->delete();
        return (new ReplyResource($reply))
            ->response()
            ->setStatusCode(200);
    }

    /*
    * Validation rules for saving to table
    */
    public static function validationArray():array {
        $validation = array (
            "replyText"         => 'required|string|max:255',

        );

        return $validation;
    }

    /*
     * Validation messages
     */
    public static function validationMessages():array {
        $validationMessages = array (
            'replyText.required' => 'This reply text field is required',
        );
        return $validationMessages;
    }
}
