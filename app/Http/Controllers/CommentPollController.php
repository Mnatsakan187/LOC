<?php

namespace App\Http\Controllers;

use App\Commentable;
use App\Http\Resources\CommentCollection;
use App\Http\Resources\CommentResource;
use App\Comment;
use App\Poll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class CommentPollController extends Controller
{
    /**
     * @OA\Get(
     *     path="user/polls/{pollId}/comments",
     *     summary="Get a list of comments based on pollId",
     *     tags={"CommentsPoll"},
     *     description="Returns all comments from the system that the user has access to.",
     *     operationId="findCommentsPoll",
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
    public function index(Request $request, $pollId)
    {
        $poll = Poll::where('user_id', Auth::user()->id)->where('id', $pollId)->first();

        $pollComments =  $poll->comments()->get();

        // Search for summary text
        if ($request->has('search')) {
            $pollComments->where('name', $request->input('search'));
        }

        return (new CommentCollection($pollComments))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Post(
     *     path="user/polls/{pollId}/comments",
     *     operationId="addComment to Poll",
     *     description="Creates a new Comment for Poll",
     *     summary="Create a new Comment for a Poll",
     *     tags={"CommentsPoll"},
     *     @OA\Response(
     *         response=200,
     *         description="Poll response",
     *         @OA\JsonContent(ref="#/components/schemas/Comment")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function store(Request $request, $pollId)
    {
        $validator = Validator::make($request->all(),  self::validationArray());

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->messages()
            ], 400);
        }

        $poll = Poll::find($pollId);

        $comment = Comment::create([
            'user_id'           => Auth::user()->id,
            'comment_text'      => $request->commentText
        ]);

        $poll->comments()->attach([$comment->id]);

        $pollComments = $poll->comments()->where('comments.id', $comment->id)->first();

        return (new CommentResource($pollComments))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="user/polls/{pollId}/comments/{commentId}",
     *     summary="Get a single Comment based on the  pollId and commentId",
     *     tags={"CommentsPoll"},
     *     description="Returns a single Comment",
     *     operationId="showComment",
     *     @OA\Parameter(
     *         name="pollId",
     *         in="path",
     *         description="id of the Poll",
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
    public function show($pollId, $commentId)
    {
        $poll = Poll::where('user_id', Auth::user()->id)->where('id', $pollId)->first();

        $pollComment = $poll->comments()->where('comments.id', $commentId)->first();

        return (new CommentResource($pollComment))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Put(
     *     path="user/polls/{pollId}/comments/{commentId}",
     *     operationId="edit Comment based on the Poll",
     *     description="Edits a Comment based on the Poll",
     *     summary="Edit a Comment based on the Poll",
     *     tags={"CommentsPoll"},
     *     @OA\Parameter(
     *         name="pollId",
     *         in="path",
     *         description="id of the Poll",
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
    public function update(Request $request, $pollId, $commentId)
    {
        $validator = Validator::make($request->all(), self::validationArray());

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->messages()
            ], 400);
        }

        $poll = Poll::where('user_id', Auth::user()->id)->where('id', $pollId)->first();

        $pollComments = $poll->comments()->where('comments.id', $commentId)->first();

        $pollComments->update([
            'user_id'           => Auth::user()->id,
            'comment_text'      => $request->commentText
        ]);

        return (new CommentResource($pollComments))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Delete(
     *     path="user/polls/{pollId}/comments/{commentId}",
     *     description="Delete a single Comment based on the  pollId",
     *     operationId="deleteComment based on the  pollId",
     *     summary="Delete a Comment based on the  pollId",
     *     tags={"CommentsPoll"},
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
    public function destroy($pollId, $commentId)
    {
        $poll = Poll::where('user_id', Auth::user()->id)->where('id', $pollId)->first();

        $pollComments = $poll->comments()->where('comments.id', $commentId)->first();

        Commentable::where('comment_id', $commentId)->where('commentable_id', $pollId)
            ->where('commentable_type', 'App\Poll')->delete();

        $pollComments->delete();

        return (new CommentResource($pollComments))
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
