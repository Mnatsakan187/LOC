<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 *  @OA\Schema(
 *   schema="Poll",
 *   type="object",
 *   allOf={
 *       @OA\Schema(
 *           required={"id", "userId", "profileId", "question", "answerType"},
 *           @OA\Property(property="id", format="int64", type="integer"),
 *           @OA\Property(property="userId",  format="int64", type="integer"),
 *           @OA\Property(property="profileId",  format="int64", type="integer"),
 *           @OA\Property(property="name",   type="string"),
 *           @OA\Property(property="question",   type="string"),
 *           @OA\Property(property="answerType",  format="int64", type="integer"),
 *           @OA\Property(property="answer",      type="string"),
 *           @OA\Property(property="createdAt", type="datetime"),
 *           @OA\Property(property="updatedAt", type="datetime"),
 *           @OA\Property(property="deletedAt", type="datetime"),
 *
 *       )
 *   }
 * )
 */

/**
 * @property-read UserPollAnswer|\Illuminate\Database\Eloquent\Collection $userPollAnswers
 *
 * Class Poll
 * @package App
 */
class Poll extends Model
{
    protected $table = 'polls';
    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id'                   ,
        'user_id'              ,
        'profile_id'           ,
        'project_id'           ,
        'name'                 ,
        'question'             ,
        'answer_type'          ,
        'answer'               ,
        'created_at'           ,
        'updated_at'           ,
        'deleted_at'           ,
        'pin_to_top'           ,

    ];

    public static $responseBody = [
        'id'                   ,
        'userId'               ,
        'profileId'            ,
        'projectId'            ,
        'name'                 ,
        'question'             ,
        'answerType'           ,
        'answer'               ,
        'createdAt'            ,
        'updatedAt'            ,
        'deletedAt'            ,
        'pinToTop'             ,
    ];


    /**
     * Get the answers for the poll.
     */
    public function answers()
    {
        return $this->hasMany('App\Answer');
    }


    /**
     * Get the pollAnswer record associated with the poll.
     */
    public function userPollAnswers()
    {
        return $this->hasMany('App\UserPollAnswer');
    }


    /**
     * Get all of the likes for the event.
     */
    public function likes()
    {
        return $this->morphToMany(Like::class, 'likeable');
    }

    /**
     * Get all of the comments for the post.
     */
    public function comments()
    {
        return $this->morphToMany(Comment::class, 'commentable', 'commentable');
    }

    /**
     * Get all of the notifications for the project.
     */
    public function notifications()
    {
        return $this->morphToMany(Notification::class, 'notificationable', 'notificationables');
    }

    /**
     * Get all of the profile for the post.
     */
    public function profile()
    {
        return $this->belongsTo(Profile::class, 'profile_id', 'id');
    }

    /**
     * Get all of the project for the post.
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }


    /**
     * Сheck whether it is necessary to block the possibility of an answer?
     *
     * @param User $user
     * @return bool
     */
    public function doNeedToDisableRespond(User $user)
    {
        $disable = false;

        $this->userPollAnswers->map(function ($item, $key) use ($user, &$disable) {

            /** @var UserPollAnswer $item */

            if ($item->user->is($user)) $disable = true;
        });

        return $disable;
    }

    /**
     * Get user pool answer for User
     *
     * @param User $user
     * @return UserPollAnswer|null
     */
    public function getUserPoolAnswerForUser(User $user)
    {
        $userPoolAnswer = null;

        $this->userPollAnswers->map(function ($item, $key) use ($user, &$userPoolAnswer) {

            /** @var UserPollAnswer $item */

            if ($item->user->is($user)) $userPoolAnswer = $item;
        });

        return $userPoolAnswer;
    }
}
