<?php

namespace App\Http\Resources;

use App\Project;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public $preserveKeys    = true;
    protected $primaryKey   = 'id';
    protected $table        = 'users';

    protected $withoutFields = ['password','emailVerifiedAt'];



    public function toArray($request)
    {
        $userMessages =   $this->myMessages()->count();

        return $this->filterFields([
            "id"                                        => $this["id"],
            "firstName"                                 => $this["first_name"],
            "lastName"                                  => $this["last_name"],
            "preferredPronoun"                          => $this["preferred_pronoun"],
            "dateOfBirth"                               => $this["date_of_birth"],
            "location"                                  => $this["location"],
            "contentPreferenceWritten"                  => $this["content_preference_written"],
            "contentPreferenceAudio"                    => $this["content_preference_audio"],
            "contentPreferenceVisual"                   => $this["content_preference_visual"],
            "contentPreferenceEvents"                   => $this["content_preference_events"],
            "subscriptionId"                            => $this["subscription_id"], // Taron TODO: Is this column supposed to be in this table?
            "streetAddress"                             => $this["street_address"],
            "isVerified"                                => $this["is_verified"],
            "emailVerifiedAt"                           => $this["email_verified_at"],
            "email"                                     => $this["email"],
            "isDeleted"                                 => $this["is_deleted"],
            "accountType"                               => $this["account_type"],
            "isEnabled"                                 => $this["is_enabled"],
            "updatedBy"                                 => $this["updated_by"],
            "createdBy"                                 => $this["created_by"],
            "createdAt"                                 => $this["created_at"],
            "updatedAt"                                 => $this["updated_at"],
            "password"                                  => $this["password"],
            "passwordConfirmation"                      => $this["password_confirmation"],
            "emailToken"                                => $this["email_token"],
            "rememberToken"                             => $this["remember_token"],
            "avatarUri"                                 => $this["avatar_uri"],
            "backgroundUri"                             => $this["background_uri"],
            "profileId"                                 => $this["follow_by_user_id"],
            "subscriptions"                             => isset($this->subscriptions) ? $this->subscriptions : '',
            "collections"                               => isset($this->collections) ? $this->collections : '',
            'hudType'                                   => $this["account_type"] == 1 ?  'creator' : 'user',
            "firstTimeUser"                             => $this["first_time_user"],
            'messagesCreatedAt'                         => isset($this->myMessages()->orderBy('created_at')->first()->created_at) ? $this->myMessages()->orderBy('created_at')->first()->created_at : '',
            'messagesReceiveCount'                      => isset($userMessages) ? $this->myMessages()->count() : '',
            'messagesSentCount'                         => $this->messagesToMe()->count(),


        ]);
    }

    public function with($request) {

        return [
            'self' => url()->current()
        ];
    }

    /**
     * Set the keys that are supposed to be filtered out.
     *
     * @param array $fields
     * @return $this
     */
    public function hide(array $fields)
    {
        $this->withoutFields = $fields;
        return $this;
    }
    /**
     * Remove the filtered keys.
     *
     * @param $array
     * @return array
     */
    protected function filterFields($array)
    {
        return collect($array)->forget($this->withoutFields)->toArray();
    }
}
