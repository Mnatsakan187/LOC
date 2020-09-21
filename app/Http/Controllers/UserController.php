<?php

namespace App\Http\Controllers;


use App\Connect;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Mail\SendResetPasswordEmail;
use App\Mailshake;
use App\Media;
use App\Notifications\ResetPassword;
use App\PasswordReset;
use App\Profile;
use App\Rules\ValidationRecaptcha;
use App\SocialMediaLink;
use App\Subscription;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
//use Tymon\JWTAuth\Contracts\Providers\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Validator;
use Hash;
use App\Mail\SendVerificationEmail;
use Illuminate\Support\Facades\Mail;
use Newsletter;
use Laravel\Socialite\Facades\Socialite;



class UserController extends Controller
{

    public function index(Request $request)
    {
        $users = User::orderBy('id');

        if(isset($request->creators) && $request->creators == 1){
            $users  = $users->where('account_type', 1)->get();
        }else if(isset($request->users) && $request->users == 1){
            $users  = $users->where('account_type', 0)->get();
        }else if($request->exception == 1) {
            $users  = $users->where('id', '!=', Auth::user()->id)->get();
        }else if($request->exception == 2){
            $users  = $users->where('users.id', '!=', Auth::user()->id)->get();
        }else {
            $users  = $users->get();
        }
        return (new UserCollection($users))
            ->response()
            ->setStatusCode(200);
    }

    /**

     * @OA\POST(
     *     path="/user",
     *     operationId="newUser",
     *     description="Registers a new user to the system. The user needs to be validated after this step",
     *     summary="Create a new user",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         description="User parameters",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/body",
     *             @OA\Schema(ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successfully created",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), $this->validationArray($request), $this->validationMessages());

        if ($validator->fails()) {
            return response()->json(
                [
                    'error' => $validator->messages()
                ], 400);
        }


        $user = User::create([
            'first_name'                    => $request['firstName'],
            'last_name'                     => $request['lastName'],
            'account_type'                  => $request['accountType'],
            'email'                         => $request['email'],
            'password'                      => Hash::make($request['password']),
            'password_confirmation'         => Hash::make($request['password_confirmation']),
            'street_address'                => $request['streetAddress'],
            'updated_by'                    => $request['updatedBy'],
            'preferred_pronoun'             => 'test',
            'creative_title'                => $request['creativeTitle'],
            'date_of_birth'                 => Carbon::createFromFormat('d-m-Y', $request->dateOfBirth, 'Europe/London'),
            'location'                      => $request['location'],
            'verified'                      => $request['verified'],
            'content_preference_written'    => $request['contentPreferenceWritten'],
            'content_preference_audio'      => $request['contentPreferenceAudio'],
            'content_preference_visual'     => $request['contentPreferenceVisual'],
            'content_preference_events'     => $request['contentPreferenceEvents'],
            'subscription_id'               => $request['subscriptionId'],
            'email_token'                   => sha1(time()),
            'is_verified'                   => $request['isVerified'] ? $request['isVerified'] : 0,
            'avatar_uri'                    => $request->file('avatarUri') ? $request->file('avatarUri')->hashName() : '',
            'background_uri'                => $request->file('backgroundUri') ? $request->file('backgroundUri')->hashName() : ''
        ]);

        $mailshakesAPI =  Mailshake::getcampaigns();
        $mailshakesAPI = json_decode($mailshakesAPI);
        if(isset($mailshakesAPI->results)){
            foreach ($mailshakesAPI->results as $item) {
                $mailshakes = Mailshake::where('campaign_id',$item->id)->first();
                if(!isset($mailshakes->id)){
                    Mailshake::create([
                        'campaign_id' => $item->id,
                        'title' => trim($item->title)
                    ]);
                }
            }
        }


        $mailShakeSetCampaignRecepientsArray = [];
        $mailShakeCampaignIDs = Mailshake::pluck('campaign_id')->toArray();
        // Loop mailshake campaigns
        foreach ($mailShakeCampaignIDs as $mailShakeCampaignId){
            $campaignRecepients = [];
            // Loop all updated subscriptions
            $campaignRecepients[] = [
                'emailAddress'=>$user->email,
                'fullName'=>$user->first_name. ' '. $user->last_name,
                "fields"=>["addonName"=>'LOC']
            ];

            $mailShakeSetCampaignRecepientsArray[$mailShakeCampaignId] = [
                'campaignID' => $mailShakeCampaignId,
                'addAsNewList' => true,
                'addresses' => $campaignRecepients
            ];

        }

        foreach ($mailShakeSetCampaignRecepientsArray as $mailshakeCampaignWithRecipients){
            if($mailshakeCampaignWithRecipients['campaignID'] == 295353) {
                Mailshake::runcroncampaign($mailshakeCampaignWithRecipients);
            }

        }

        if ($request->hasFile('avatarUri')) {

            $image = $request->file('avatarUri');

            Storage::disk('public')->putFile('avatarImage/'.$user->id, $image);

        }

        if ($request->hasFile('backgroundUri')) {

            $image = $request->file('backgroundUri');

            Storage::disk('public')->putFile('backgroundImage/'.$user->id, $image);

        }

        if(isset($request->creativeTitle)) {

            $profile = Profile::create([
                'user_id'           => $user->id,
                'creative_title'    => $request['creativeTitle'],
                'biography'         => $request['biography'],
                'location'          => $request['location'],
                'avatar_uri'        => $request->file('avatarUri') ? $request->file('avatarUri')->hashName() : '',
                'background_uri'    => $request->file('backgroundUri') ? $request->file('backgroundUri')->hashName() : '',
            ]);

            if ($request->hasFile('avatarUri')) {

                $image = $request->file('avatarUri');

                Storage::disk('public')->putFile('profiles/profilePictureImage/'.$profile->id, $image);

            }

            if ($request->hasFile('backgroundUri')) {

                $image = $request->file('backgroundUri');

                Storage::disk('public')->putFile('profiles/profileBackgroundImage/'.$profile->id, $image);

            }

        }

        if(isset($request->socials) && !empty($request->socials)){
            $socialsArray = json_decode($request->socials);
            $arr = [];
            foreach ($socialsArray as $key => $value){
                foreach ($value as $iKey  => $iValue){
                    $arr[] = [
                        'user_id'           => $user->id,
                        'profile_id'        => $profile->id,
                        'name'              => $iKey,
                        'social_media_link' => $iValue,
                        'created_at'        => Carbon::now()->toDateTimeString(),
                        'updated_at'        => Carbon::now()->toDateTimeString(),
                    ];
                }
            }

            SocialMediaLink::insert($arr);
        }

        if ($user && !isset($request->test)) {
            $email = new SendVerificationEmail($user);
            Mail::to($user->email)->send($email);
        }

        $data = new UserResource($user);

        return response()->json(compact('data'),201);
    }

    /**
     * @OA\POST(
     *     path="/user/logout",
     *     operationId="userLogout",
     *     description="User Logout",
     *     summary="Logout user ",
     *     tags={"Users"},
     *     @OA\Response(
     *         response=200,
     *         description="Get all parameters",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function userLogout(Request $request)
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        $data = new UserResource(Auth::user());

        return response()->json(compact('data'),200);
    }

    /**
     * @OA\POST(
     *     path="/user/login",
     *     operationId="userLogin",
     *     description="Logs the user in and provides a JWT token on the header",
     *     summary="Authenticates the user ",
     *     tags={"Users"},
     *     @OA\Response(
     *         response=200,
     *         description="Get all parameters",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function authenticate(Request $request)
    {
        $kNo = 0;

        $credentials = $request->only('email', 'password');

        $rules = [
            'email' => 'required|email',
            'password' => 'required',
        ];

        $validator = Validator::make($credentials, $rules);

        if($validator->fails()) {
            return response()->json([
                'error'=> $validator->messages()
            ], 400);
        }

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'error' => ['email' =>   [trans('auth.failed')]]
                ], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        // Is the user is not validated, he cannot login
        $user = User::findOrFail(Auth::user()->id);
        if($user->is_verified == $kNo){
            return response()->json([
                'error' => ['verify_error' =>   [trans('auth.failed_login_verification')]]
            ], 400);
        }

        // Everything is fine, return User model
        $data = new UserResource(Auth::user());

        return response()->json(compact('data'),202, ['token'=>$token]);
    }

    /*
     * Gets a User object from the currently logged in user or gives error responses
     *
     */
    private function getCurrentUser() {
        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());
        }

        return $user;
    }

    /**
     * @OA\GET(
     *     path="/user",
     *     operationId="getUser",
     *     description="Gets the information of the user",
     *     summary="Get user data",
     *     tags={"Users"},
     *     @OA\Response(
     *         response=200,
     *         description="Get all parameters",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function show(Request $request)
    {
        $user = $this->getCurrentUser();

        $data = new UserResource($user);

        return response()->json(compact('data'),200);
    }

    /**
     * @OA\Put(
     *     path="/user/validate/{token}",
     *     operationId="validateUser",
     *     description="Validates a user with a link from an email",
     *     summary="Validate the user",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="token",
     *         in="path",
     *         description="id of the user",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *      @OA\Response(
     *         response=201,
     *         description="Validation token no longer valid",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function verify(Request $request, $token)
    {

        $user = User::where('email_token', $token)->first();
        if ($user) {
            $user->is_verified = 1;
            $user->email_token = '';
            $user->email_verified_at = Carbon::now();;
            $user->save();

            return response()->json(compact('user'),202);
        } else {

            return response()->json('',401);
        }
    }

    /**
     * @OA\Put(
     *     path="/user",
     *     operationId="editUser",
     *     description="Edits currently logged userr",
     *     summary="Edit the user",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         description="User parameters",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Issue response",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function update(Request $request)
    {
        /**
         * @var User $user
         */
        $user = $this->getCurrentUser();

        $validator = Validator::make($request->all(), $this->updateValidationArray($request), $this->validationMessages());

        if ($validator->fails()) {
            return response()->json([
                    'error' => $validator->messages()
                ], 400);
        }

        $user->update([
            'first_name'                    => $request->firstName,
            'last_name'                     => $request->lastName,
            'email'                         => $request->email,
            'street_address'                => $request->streetAddress,
            'updated_by'                    => Auth::user()->id,
            'preferred_pronoun'             => 'test',
            'date_of_birth'                 => $request->dateOfBirth,
            'location'                      => $request->location,
            'content_preference_written'    => $request['contentPreferenceWritten'],
            'content_preference_audio'      => $request['contentPreferenceAudio'],
            'content_preference_visual'     => $request['contentPreferenceVisual'],
            'content_preference_events'     => $request['contentPreferenceEvents'],
            'background_uri'                => $request['backgroundUri'],
        ]);

        if($request->hasFile('avatarUri')){

            $user = $user->fresh();

            $image = $request->file('avatarUri');

            Storage::disk('public')->putFile('avatarImage/'.$user->getKey(), $image);

            $avatarImage = $image->hashName();

            $user->update(['avatar_uri' => $avatarImage ]);

        }

        $data = new  UserResource($user->fresh());

        return response()->json(compact('data'),200);
    }

    /**
     * @OA\Put(
     *     path="/user/change-password",
     *     operationId="editUserPassword",
     *     description="Edits currently logged user's passwordr",
     *     summary="Edit the user password",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         description="Password and password confirmation must be provided on the body ONLY",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Issue response",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function chageUserPassword(Request $request){
        $user = $this->getCurrentUser();

        $validator = Validator::make($request->all(), self::passwordChangeValidationArray($request));

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->messages()
            ], 400);
        }

        $userModel = User::findOrFail($user->id);

        $userModel->update([
            'password'                      => Hash::make($request['password']),
            'password_confirmation'         => Hash::make($request['password_confirmation']),
        ]);

        $data = new UserResource($user);

        return response()->json(compact('data'),200);
    }
    /**
     * @OA\Delete(
     *     path="/user",
     *     description="Delete the current logged user. Soft deletes are applied",
     *     operationId="deleteUser",
     *     summary="Delete an user",
     *     tags={"Users"},
     *     @OA\Response(
     *         response=410,
     *         description="user deleted"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Does not exist"
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/ErrorModel")
     *     )
     * )
     */
    public function destroy(Request $request)
    {
        $user = $this->getCurrentUser();

        $userModel = User::findOrFail($user->id);

        $userModel->delete();

        $data = new UserResource($userModel);

        return response()->json(compact('data'),410);
    }

    /*
    * Validation rules for saving to table
    */
    public static function validationArray(Request $request):array {

        $user = (!Auth::user()) ? null : Auth::user()->id ;

        $validation = array (
            'firstName'          => 'required|string|max:255',
            'lastName'           => 'required|string|max:255',
            'email'              => 'required|unique:users,email,'.  $user,
            'password'           => 'required|min:6',
            'accountType'        => 'required|integer',
        );

        return $validation;
    }

    public function validateUserEmail(Request $request)
    {
        $validator =  Validator::make($request->all(), [
            'email' => 'required|unique:users,email,',
            'recaptchaToken' => ['required', new ValidationRecaptcha()]
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->messages()
            ], 400);
        }

        return response()->json(['success'], 200);
    }


    public function sendResetPasswordEmail(Request $request)
    {
        $validator =  Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->messages()
            ], 400);
        }

        $user = User::where('email', $request->email)->first();

        if(!isset($user)){
            return response()->json([
                'error' => ['email' =>   [trans('auth.failed_email')]]
            ], 400);
        }

        if (!$user->is_verified) {
            return response()->json([
                'error' => ['email' =>   [trans('auth.failed_reset')]]
            ], 400);
        } else {
            $passwordReset = PasswordReset::create([
                    'token' =>  sha1(time()),
                    'email' => $request->email,
                ]);

            if ($user) {
                $data = [
                    'firstName' =>  $user->first_name,
                    'token'  =>    $passwordReset->token,
                ];
                $email = new SendResetPasswordEmail($data);
                Mail::to($user->email)->send($email);
            }

            return response()->json(['success'], 200);
        }
    }


    public function resetPassword(Request $request)
    {

        Validator::make($request->all(), [
            'token' => 'required|string',
            'recaptchaToken' => ['required', new ValidationRecaptcha()],
            'password'                  => 'required|min:6|confirmed'
        ]);

        $passwordReset = PasswordReset::where('token', $request->token)->first();

        User::where('email', $passwordReset->email)->update(['password' => Hash::make($request->password)]);

        PasswordReset::where('token', $request->token)->delete();

        return response()->json(['success'], 200);
    }


    /**
     * Facebook redirect.
     *
     * @return void
     */
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->stateless()->redirect();
    }

    /**
     * Facebook handel.
     *
     * @return void
     */
    public function handleFacebookCallback()
    {
        try {
            $user = Socialite::driver('facebook')->stateless()->user();
            $findUser = User::where('facebook_id', $user->id)->first();
            if($findUser){
                $userToken=JWTAuth::fromUser($findUser);
                return redirect('/login')->withCookie(cookie('tokenKey', $userToken,0.1));
            }else{
                $lastNameFirstName = explode(" ", $user->user['name']);
                $validator = Validator::make(['email'],
                    [
                        'email'  => 'required|unique:users,email,',
                    ]
                );
                $user = User::create([
                          'first_name' => $lastNameFirstName[0],
                          'last_name' => $lastNameFirstName[1],
                          'email'=> isset($user->user['email']) && !$validator->fails() ? $user->user['email'] : $lastNameFirstName[0]. '@gmail.com',
                          'account_type'=> 0,
                          'preferred_pronoun'=> 'test',
                          'facebook_id'=> $user->id,
                          'password'=> Hash::make($user->id)
                        ]);
                $userToken=JWTAuth::fromUser($user);
                return redirect('/login')->withCookie(cookie('tokenKey', $userToken,0.1));
            }
        } catch (Exception $e) {

            return redirect('login');
        }
    }


    /**
     * Twitter redirect.
     *
     * @return void
     */
    public function redirectToTwitter()
    {
        return Socialite::with('twitter')->stateless()->redirect();
    }

    /**
     * Twitter handel.
     *
     * @return void
     */
    public function handleTwitterCallback()
    {
        try {
            $user = Socialite::driver('twitter')->stateless()->user();
            $findUser = User::where('twitter_id', $user->id)->first();
            if($findUser){
                $userToken=JWTAuth::fromUser($findUser);
                return redirect('/login')->withCookie(cookie('tokenKey', $userToken,0.1));
            }else{
                $user = User::create([
                    'first_name' => $user->name,
                    'last_name' => $user->name,
                    'email'=> $user->nickname.'@gmail.com',
                    'account_type'=> 0,
                    'preferred_pronoun'=> 'test',
                    'twitter_id'=> $user->id,
                    'password'=> Hash::make($user->id)
                ]);
                $userToken=JWTAuth::fromUser($user);
                return redirect('/login')->withCookie(cookie('tokenKey', $userToken,0.1));
            }
        } catch (Exception $e) {
            return redirect('login');
        }
    }


    /**
     * Instagram redirect.
     *
     * @return void
     */
    public function redirectToInstagram()
    {
        return Socialite::with('instagram')->stateless(false)->redirect();
    }


    /**
     * Instagram handel.
     *
     * @return void
     */
    public function handleInstagramCallback()
    {
        try {
            $user = Socialite::driver('instagram')->stateless(false)->user();
            $findUser = User::where('instagram_id', $user->user['id'])->first();
            if($findUser){
                $userToken = JWTAuth::fromUser($findUser);
                return redirect('/login')->withCookie(cookie('tokenKey', $userToken,0.1));
            }else{
                $lastNameFirstName = explode(" ", $user->user['full_name']);
                $user = User::create([
                            'first_name' => $lastNameFirstName[0],
                            'last_name' => $lastNameFirstName[1],
                            'email'=> $user->user['username'].'@gamil.com',
                            'account_type'=> 0,
                            'preferred_pronoun'=> 'test',
                            'instagram_id'=> $user->user['id'],
                            'password'=> Hash::make($user->user['id'])
                        ]);
                $userToken = JWTAuth::fromUser($user);
                return redirect('/login')->withCookie(cookie('tokenKey', $userToken,0.1));
            }
        } catch (Exception $e) {

            return redirect('login');
        }
    }


    /**
     * Linkedin redirect.
     *
     * @return void
     */
    public function redirectToLinkedin()
    {
        return Socialite::driver('linkedin')->stateless()->redirect();
    }


    /**
     * Linkedin handel.
     *
     * @return void
     */
    public function handleLinkedinCallback()
    {
        try {
            $user = Socialite::driver('linkedin')->stateless()->user();
            $findUser = User::where('linkedin_id', $user->id)->first();
            if($findUser){
                $userToken = JWTAuth::fromUser($findUser);
                return redirect('/login')->withCookie(cookie('tokenKey', $userToken,0.1));
            }else{
                $validator = Validator::make(['email'],
                    [
                        'email'  => 'required|unique:users,email,',
                    ]
                );

                $user = User::create([
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'email'=> isset($user->email) && !$validator->fails() ? $user->email : $user->id.'@gmail.com',
                        'account_type'=> 0,
                        'preferred_pronoun'=> 'test',
                        'linkedin_id'=> $user->id,
                        'password'=> Hash::make($user->id)
                    ]);
                $userToken = JWTAuth::fromUser($user);
                return redirect('/login')->withCookie(cookie('tokenKey', $userToken,0.1));
            }
        } catch (Exception $e) {
            return redirect('login');
        }
    }


    public function updateFirstTimeUser()
    {
        $user = $this->getCurrentUser();

        $userModel = User::findOrFail($user->id);

        $userModel->update([
            'first_time_user' => 1,
        ]);

        $data = new UserResource($user);
        return response()->json(compact('data'),200);
    }

   /*
   * Validation rules for saving to table
   */
   public static function updateValidationArray(Request $request):array {
        $validation = array (
            'firstName'          => 'required|string|max:255',
            'lastName'           => 'required|string|max:255',
            'email'              => 'required|unique:users,email,'. Auth::user()->id,
        );

        return $validation;
    }

   /*
   * Validation rules for saving to table
   */
    public static function passwordChangeValidationArray(Request $request):array {
        $validation = array (
            'password'                  => 'required|min:6'
        );

        return $validation;
    }

    /*
     * Validation messages
     */
    public static function validationMessages():array {
        $validationMessages = array (
            'email.unique' => 'This email already exists',
        );

        return $validationMessages;
    }
}
