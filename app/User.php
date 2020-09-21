<?php

namespace App;

use App\Exceptions\UserAccountTypeException;
use App\Notifications\VerifyEmail;
use App\Notifications\ResetPassword;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;



/**
 *  @OA\Schema(
 *   schema="User",
 *   type="object",
 *   allOf={
 *       @OA\Schema(
 *           required={"id", "tenant_id"},
 *           @OA\Property(property="id", format="int64", type="integer"),
 *           @OA\Property(property="firstName", type="string"),
 *           @OA\Property(property="lastName", type="string"),
 *           @OA\Property(property="email",  type="string",),
 *           @OA\Property(property="password",  type="string"),
 *           @OA\Property(property="emailVerifiedAt",  type="datetime"),
 *           @OA\Property(property="rememberToken", type="string"),
 *           @OA\Property(property="streetAddress",  type="string"),
 *           @OA\Property(property="accountType", format="int64", type="integer"),
 *           @OA\Property(property="updatedBy", format="int64", type="integer"),
 *           @OA\Property(property="displayName", type="string"),
 *           @OA\Property(property="creativeTitle", type="string"),
 *           @OA\Property(property="dateOfBirth",  type="datetime"),
 *           @OA\Property(property="location",  type="string"),
 *           @OA\Property(property="contentPreferenceWritten", format="int64", type="integer"),
 *           @OA\Property(property="contentPreferenceAudio", format="int64", type="integer"),
 *           @OA\Property(property="contentPreferenceVisual", format="int64", type="integer"),
 *           @OA\Property(property="contentPreferenceEvents", format="int64", type="integer"),
 *           @OA\Property(property="subscriptionId", format="int64", type="integer"),
 *           @OA\Property(property="createdAt", type="datetime"),
 *           @OA\Property(property="updatedAt", type="datetime")
 *
 *       )
 *   }
 * )
 */

/**
 * @property-read \Illuminate\Database\Eloquent\Collection|Hide[] $hides
 *
 * @property int $account_type
 *
 * Class User
 * @package App
 */
class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $table = 'users';
    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'first_name'                    ,
        'last_name'                     ,
        'email'                         ,
        'password'                      ,
        'email_verified_at'             ,
        'remember_token'                ,
        'street_address'                ,
        'account_type'                  ,
        'updated_by'                    ,
        'preferred_pronoun'             ,
        'date_of_birth'                 ,
        'location'                      ,
        'content_preference_written'    ,
        'content_preference_audio'      ,
        'content_preference_visual'     ,
        'content_preference_events'     ,
        'subscription_id'               ,
        'is_verified'                   ,
        'email_token'                   ,
        'avatar_uri'                    ,
        'background_uri'                ,
        'facebook_id'                   ,
        'linkedin_id'                   ,
        'instagram_id'                  ,
        'twitter_id'                    ,
        'first_time_user'               ,
        'created_at'                    ,
        'updated_at'                    ,
    ];

    public static $responseBody = [
        'id',
        "firstName"                      ,
        "lastName"                       ,
        "email"                          ,
        "password"                       ,
        "emailVerifiedAt"                ,
        "rememberToken"                  ,
        "streetAddress"                  ,
        "accountType"                    ,
        "updatedBy"                      ,
        "preferredPronoun"               ,
        "dateOfBirth"                    ,
        "location"                       ,
        "contentPreferenceWritten"       ,
        "contentPreferenceAudio"         ,
        "contentPreferenceVisual"        ,
        "contentPreferenceEvents"        ,
        "subscriptionId"                 ,
        "emailToken"                     ,
        'avatarUri'                      ,
        'backgroundUri'                  ,
        'isVerified'                     ,
        'facebookId'                     ,
        'linkedinId'                     ,
        'instagramId'                    ,
        'twitterId'                      ,
        'firstTimeUser'                  ,
        "createdAt"                      ,
        "updatedAt"                      ,
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
         'remember_token'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'emailVerifiedAt' => 'datetime',
    ];

    const ACCOUNT_TYPE_USER = 'user';

    const ACCOUNT_TYPE_CREATOR = 'creator';

    const ACCOUNT_TYPES = [
        'user' => 0,
        'creator' => 1
    ];

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail);
    }

    /**
     * @return int
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array {
        return[];
    }

    /**
     * Get the Projects for the user.
     */
    public function projects()
    {
        return $this->hasMany('App\Project', 'user_id', 'id');
    }


    /**
     * Get the Events for the user.
     */
    public function events()
    {
        return $this->hasMany('App\Event', 'user_id', 'id');
    }

    /**
     * Get the People I Follow for the user.
     */
    public function follows()
    {
        return $this->hasMany('App\FollowUser', 'user_id', 'id');
    }

    /**
     * Get the Groups I own.
     */
    public function groups()
    {
        return $this->belongsToMany('App\Group');
    }

    /**
     * Get the  likes .
     */
    public function likes()
    {
        return $this->hasMany('App\Like');
    }


    /**
     * Get my sent messages
     */
    public function myMessages()
    {
        return $this->hasMany('App\Message', 'from_user_id', 'id');
    }

    /**
     * Get my messages to me
     */
    public function messagesToMe()
    {
        return $this->hasMany('App\Message', 'to_user_id', 'id');
    }


    /**
     * Get my profiles
     */
    public function profiles()
    {
        return $this->hasMany('App\Profile', 'user_id', 'id');
    }


    /**
     * Get my shares
     */
    public function shares()
    {
        return $this->hasMany('App\Share', 'user_id', 'id');
    }

    /**
     * Get all of the medium for the project.
     */
    public function medium()
    {
        return $this->morphToMany(Media::class, 'mediable');
    }

    /**
     * Get the subscriptions for the user.
     */
    public function subscriptions()
    {
        return $this->hasMany('App\Subscription');
    }

    /**
     * Get all of the collections for the creator.
     */
    public function collections()
    {
        return $this->morphToMany(Collection::class, 'collectionable', 'collectionables', 'collection_id', 'collectionable_id');
    }

    /**
     * Get all of the notifications for the project.
     */
    public function notifications()
    {
        return $this->morphToMany(Notification::class, 'notificationable', 'notificationables');
    }

    public function netWorks()
    {
        return $this->belongsToMany('App\User', 'networks', 'user_id', 'network_user_id');
    }


    /**
     * Get the People I Follow for the user.
     */
    public function connection()
    {
        return $this->hasMany('App\Connect', 'user_id', 'id');
    }


    /**
     * Get the Groups for the user.
     */
    public function groupsUser()
    {
        return $this->hasMany('App\Group', 'user_id', 'id');
    }


    /**
     * Get the Connected Profiles for the user.
     */
    public function connectionProfile()
    {
        return $this->belongsToMany('App\Profile', 'connection',  'user_id', 'profile_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hides()
    {
        return $this->hasMany('App\Hide', 'user_id');
    }

    /**
     * Update user account type
     *
     * @param string $type
     * @return bool
     * @throws UserAccountTypeException
     */
    public function updateAccountTypeTo(string $type)
    {
        if (!in_array($type, array_keys(self::ACCOUNT_TYPES))) {
            throw new UserAccountTypeException('unsupported user account type', 415);
        }

        if ($this->account_type == $accountTypeToUpdate = self::ACCOUNT_TYPES[$type]) {
            return true;
        } else {
            return $this->update([
                'account_type' => $accountTypeToUpdate
            ]);
        }
    }

}
