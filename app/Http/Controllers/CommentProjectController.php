<?php

namespace App\Http\Controllers;

use App\Commentable;
use App\Http\Resources\CommentCollection;
use App\Http\Resources\CommentResource;
use App\Project;
use App\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class CommentProjectController extends Controller
{
    /**
     * @OA\Get(
     *     path="user/projects/{projectId}/comments",
     *     summary="Get a list of comments based on projectId",
     *     tags={"CommentProject"},
     *     description="Returns all comments from the system that the user has access to.",
     *     operationId="findCommentProject",
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
    public function index(Request $request, $projectId)
    {
        $project = Project::where('user_id', Auth::user()->id)->where('id', $projectId)->first();

        $projectComments =  $project->comments()->get();

        // Search for summary text
        if ($request->has('search')) {
            $projectComments->where('name', $request->input('search'));
        }

        return (new CommentCollection($projectComments))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Post(
     *     path="user/projects/{projectId}/comments",
     *     operationId="addComment to Project",
     *     description="Creates a new Comment for Project",
     *     summary="Create a new Comment for a Project",
     *     tags={"CommentProject"},
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
    public function store(Request $request, $projectId)
    {
        $validator = Validator::make($request->all(),  self::validationArray());

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->messages()
            ], 400);
        }

        $project = Project::find($projectId);

        $comment = Comment::create([
            'user_id'           => Auth::user()->id,
            'comment_text'      => $request->commentText,
        ]);

        $project->comments()->attach([$comment->id]);

        $projectComment = $project->comments()->where('comments.id', $comment->id)->first();

        return (new CommentResource($projectComment))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="user/projects/{projectId}/comments/{commentId}",
     *     summary="Get a single Comment based on the  projectId and commentId",
     *     tags={"CommentProject"},
     *     description="Returns a single Comment",
     *     operationId="showComment",
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
    public function show($projectId, $commentId)
    {
        $project = Project::where('user_id', Auth::user()->id)->where('id', $projectId)->first();

        $projectComment = $project->comments()->where('comments.id', $commentId)->first();

        return (new CommentResource($projectComment))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Put(
     *     path="user/projects/{projectId}/comments/{commentId}",
     *     operationId="edit Comment based on the Project",
     *     description="Edits a Comment based on the Project",
     *     summary="Edit a Comment based on the Project",
     *     tags={"CommentProject"},
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
    public function update(Request $request, $projectId, $commentId)
    {
        $validator = Validator::make($request->all(), self::validationArray());

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->messages()
            ], 400);
        }

        $project = Project::where('user_id', Auth::user()->id)->where('id', $projectId)->first();

        $projectComment = $project->comments()->where('comments.id', $commentId)->first();

        $projectComment->update([
            'user_id'           => Auth::user()->id,
            'comment_text'      => $request->commentText,
        ]);

        return (new CommentResource($projectComment))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Delete(
     *     path="user/projects/{projectId}/comments/{commentId}",
     *     description="Delete a single Comment based on the  projectId",
     *     operationId="deleteComment based on the  projectId",
     *     summary="Delete a Comment based on the  projectId",
     *     tags={"CommentProject"},
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
    public function destroy($projectId, $commentId)
    {
        $project = Project::where('user_id', Auth::user()->id)->where('id', $projectId)->first();

        $projectComment = $project->comments()->where('comments.id', $commentId)->first();

        Commentable::where('comment_id', $commentId)->where('commentable_id', $projectId)
            ->where('commentable_type', 'App\Project')->delete();
        $projectComment->delete();

        return (new CommentResource($projectComment))
            ->response()
            ->setStatusCode(200);
    }

    /*
    * Validation rules for saving to table
    */
    public static function validationArray():array {
        $validation = array (
            "commentText"         => 'required|string|max:255',

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
