<?php

namespace App\Http\Controllers;


use App\Connect;
use App\Http\Resources\NotificationCollection;
use App\Http\Resources\NotificationResource;
use App\Notificationable;
use App\Project;
use App\Notification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Validator;

class NotificationProjectController extends Controller
{
    /**
     * @OA\Get(
     *     path="user/projects/{projectId}/notification",
     *     summary="Get a list of notifications based on projectId",
     *     tags={"NotificationsProject"},
     *     description="Returns all notifications from the system that the user has access to.",
     *     operationId="findNotificationsProject",
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
     *         description="Notification response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Notification")
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
    public function index(Request $request)
    {
        $notifications = Notification::where('user_id', Auth::user()->id)
            ->orderBy('id', 'desc');

        if($request->perPage) {
            $page = $request->page;
            $perPage = $request->perPage;
            $not = $notifications->get();
            $notifications = new LengthAwarePaginator($not->forPage($page, $perPage), $not->count(), $perPage, $page);

        }else{
            $notifications = $notifications->get();
        }
        return (new NotificationCollection($notifications))
            ->response()
            ->setStatusCode(200);
    }


    /**
     * @OA\Post(
     *     path="user/projects/{projectId}/notifications",
     *     operationId="addNotification to Project",
     *     description="Creates a new Notification for Project",
     *     summary="Create a new Notification for a Project",
     *     tags={"NotificationsProject"},
     *     @OA\Response(
     *         response=200,
     *         description="Project response",
     *         @OA\JsonContent(ref="#/components/schemas/Notification")
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
        $project = Project::find($projectId);

        $notification = Notification::create([
            'user_id'           => Auth::user()->id,
            'summary'           => $request->summary,
            'created_by'        => $request->userId,
            'type'              => $request->type,
        ]);

        $project->notifications()->attach([$notification->id]);

        $projectNotification = $project->notifications()->where('notifications.id', $notification->id)->first();

        return (new NotificationResource($projectNotification))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="user/projects/{projectId}/notifications/{notificationId}",
     *     summary="Get a single Notification based on the  projectId and notificationId",
     *     tags={"NotificationsProject"},
     *     description="Returns a single Notification",
     *     operationId="showNotification",
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
     *         name="notificationId",
     *         in="path",
     *         description="id of the Notification",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Notification response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Notification")
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
    public function show($notificationId)
    {
        $notifications = Notification::where('id', $notificationId)->first();

        return (new NotificationResource($notifications))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @OA\Delete(
     *     path="user/projects/{projectId}/notifications/{notificationId}",
     *     description="Delete a single Notification based on the  projectId",
     *     operationId="deleteNotification based on the  projectId",
     *     summary="Delete a Notification based on the  projectId",
     *     tags={"NotificationsProject"},
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
     *         description="notificationId",
     *         in="path",
     *         name="notificationId",
     *         required=true,
     *         @OA\Schema(
     *             format="int64",
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Notification response",
     *         @OA\JsonContent(ref="#/components/schemas/Notification")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function destroy($notificationId)
    {
        $notification = Notification::where('id', $notificationId)->first();

        $connect = Connect::where('profile_id', $notification->profile_id)
            ->where('user_id', $notification->created_by)->first();
        if(isset($connect)){
            $connect->delete();
        }
        Notificationable::where('notification_id', $notificationId)->delete();
        $notification->delete();

        return (new NotificationResource($notification))
            ->response()
            ->setStatusCode(200);
    }


    public function markedRead()
    {
        Notification::where('user_id', Auth::user()->id)->where('is_read', '=', 0)->update(['is_read'=>1]);
    }


    public function markedReadById($id)
    {
        Notification::where('id', $id)->where('is_read', '=', 0)->update(['is_read'=>1]);
    }

    /*
    * Validation rules for saving to table
    */
    public static function validationArray():array {
        $validation = array (
            "type"         => 'required',
        );

        return $validation;
    }

    /*
     * Validation messages
     */
    public static function validationMessages():array {
        $validationMessages = array (
            'type.required' => 'This notification date field is required',
        );

        return $validationMessages;
    }
}
