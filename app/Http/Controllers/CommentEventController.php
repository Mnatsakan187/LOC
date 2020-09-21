<?php

namespace App\Http\Controllers;

use App\Commentable;
use App\Event;
use App\Http\Resources\CommentCollection;
use App\Http\Resources\CommentResource;
use App\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class CommentEventController extends Controller
{
    /**
     * @OA\Get(
     *     path="user/events/{eventId}/comments",
     *     summary="Get a list of comments based on eventId",
     *     tags={"CommentsEvent"},
     *     description="Returns all comments from the system that the user has access to.",
     *     operationId="findCommentsEvent",
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
     *             @OA\Items(ref="#/components/schemas/Comment")
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
    public function index(Request $request, $eventId)
    {
        $event = Event::where('user_id', Auth::user()->id)->where('id', $eventId)->first();

        $eventComments =  $event->comments()->get();

        // Search for summary text
        if ($request->has('search')) {
            $eventComments->where('name', $request->input('search'));
        }

        return (new CommentCollection($eventComments))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Post(
     *     path="user/events/{eventId}/comments",
     *     operationId="addComment to Event",
     *     description="Creates a new Comment for Event",
     *     summary="Create a new Comment for a Event",
     *     tags={"CommentsEvent"},
     *     @OA\Response(
     *         response=200,
     *         description="Event response",
     *         @OA\JsonContent(ref="#/components/schemas/Comment")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function store(Request $request, $eventId)
    {
        $validator = Validator::make($request->all(),  self::validationArray());

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->messages()
            ], 400);
        }

        $event = Event::find($eventId);

        $comment = Comment::create([
            'user_id'           => Auth::user()->id,
            'comment_text'      => $request->commentText
        ]);

        $event->comments()->attach([$comment->id]);

        $eventComments = $event->comments()->where('comments.id', $comment->id)->first();

        return (new CommentResource($eventComments))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="user/events/{eventId}/comments/{commentId}",
     *     summary="Get a single Comment based on the  eventId and commentId",
     *     tags={"CommentsEvent"},
     *     description="Returns a single Comment",
     *     operationId="showComment",
     *     @OA\Parameter(
     *         name="eventId",
     *         in="path",
     *         description="id of the Event",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
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
     *             @OA\Items(ref="#/components/schemas/Comment")
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
    public function show($eventId, $commentId)
    {
        $event = Event::where('user_id', Auth::user()->id)->where('id', $eventId)->first();

        $eventComment = $event->comments()->where('comments.id', $commentId)->first();

        return (new CommentResource($eventComment))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Put(
     *     path="user/events/{eventId}/comments/{commentId}",
     *     operationId="edit Comment based on the Event",
     *     description="Edits a Comment based on the Event",
     *     summary="Edit a Comment based on the Event",
     *     tags={"CommentsEvent"},
     *     @OA\Parameter(
     *         name="eventId",
     *         in="path",
     *         description="id of the Event",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="commentId",
     *         in="path",
     *         description="id of the Comment",
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
     *             @OA\Schema(ref="#/components/schemas/Comment")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comment response",
     *         @OA\JsonContent(ref="#/components/schemas/Comment")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function update(Request $request, $eventId, $commentId)
    {
        $validator = Validator::make($request->all(), self::validationArray());

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->messages()
            ], 400);
        }

        $event = Event::where('user_id', Auth::user()->id)->where('id', $eventId)->first();

        $postComments = $event->comments()->where('comments.id', $commentId)->first();

        $postComments->update([
            'user_id'           => Auth::user()->id,
            'comment_text'      => $request->commentText
        ]);

        return (new CommentResource($postComments))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Delete(
     *     path="user/events/{eventId}/comments/{commentId}",
     *     description="Delete a single Comment based on the  eventId",
     *     operationId="deleteComment based on the  eventId",
     *     summary="Delete a Comment based on the  eventId",
     *     tags={"CommentsEvent"},
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
     *         @OA\JsonContent(ref="#/components/schemas/Comment")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function destroy($eventId, $commentId)
    {
        $event = Event::where('user_id', Auth::user()->id)->where('id', $eventId)->first();

        $postComments = $event->comments()->where('comments.id', $commentId)->first();

        Commentable::where('comment_id', $commentId)->where('commentable_id', $eventId)
            ->where('commentable_type', 'App\Event')->delete();

        $postComments->delete();

        return (new CommentResource($postComments))
            ->response()
            ->setStatusCode(200);
    }

    /*
    * Validation rules for saving to table
    */
    public static function validationArray():array {
        $validation = array (
            "commentText"  => 'required|string|max:255|',
        );

        return $validation;
    }

    /*
     * Validation messages
     */
    public static function validationMessages():array {
        $validationMessages = array (
            'commentText.required' => 'This comment text field is required',
        );
        return $validationMessages;
    }
}
