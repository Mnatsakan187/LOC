<?php
//
//namespace Tests\Feature;
//
//use App\Profile;
//use App\Exceptions\Handler;
//use App\User;
//use Illuminate\Contracts\Debug\ExceptionHandler;
//use Tests\TestCase;
//use Illuminate\Foundation\Testing\WithFaker;
//
//
//class ProfileTest extends TestCase
//{
//    use WithFaker;
//    protected $user;
//    protected $httpHeader;
//    protected $cleanUpAfterTests;
//
//    /*
//     * canGetListOfProfile
//     * canNotCreateProfileWithoutCreativeTitle
//     * canNotCreateProfileWithSameCreativeTitle
//     * canCreateNewProfile
//     * canGetSingleProfile
//     * canNotUpdateProfileWithoutCreativeTitle
//     * canNotUpdateProfileWithoutSameCreativeTitle
//     * canUpdateProfile
//     * canDeleteProfile
//     */
//
//    protected function setUp():void
//    {
//        /**
//         * This disables the exception handling to display the stacktrace on the console
//         * the same way as it shown on the browser
//         */
//        parent::setUp();
//        $this->disableExceptionHandling();
//        $this->getUserForTesting();
//    }
//
//    protected function disableExceptionHandling()
//    {
//        $this->app->instance(ExceptionHandler::class, new class extends Handler {
//            public function __construct() {}
//
//            public function report(\Exception $e)
//            {
//                // no-op
//            }
//
//            public function render($request, \Exception $e) {
//                throw $e;
//            }
//        });
//    }
//
//    protected function getUserForTesting()
//    {
//        $userModel       = factory(User::class)->make()->toArray();
//
//        $userModel['test'] = 1;
//
//        // Register a new user
//        $userResponse       = $this->json('POST', 'api/v1/user/', $userModel)
//            ->assertStatus(201);
//
//        $userId     = json_decode($userResponse->getContent(), true)['data']['id'];
//
//        $user       = User::findOrFail($userId);
//        $emailToken = $user->email_token;
//
//        // Validate the new user
//        $userValidateResponse       = $this->json('GET', 'api/v1/user/validate/' . $emailToken, $userModel)
//            ->assertStatus(202);
//
//        // Login user to the system
//        $credentials = [
//            'email'     => $user->email,
//            'password'  => 123456
//        ];
//
//        $userLoginResponse       = $this->json('POST', 'api/v1/user/login' , $credentials)
//            ->assertStatus(202);
//
//        $this->user = $user;
//
//        $this->httpHeader = [
//            'Accept'        => 'application/json',
//            'Content-Type'  => 'application/json',
//            'Authorization' => 'Bearer ' . $userLoginResponse->headers->get('token')
//        ];
//    }
//
//    /** @test canGetListOfProfile */
//    public function canGetListOfProfile()
//    {
//        $profileModel             = factory(Profile::class)->make()->toArray();
//
//        $profileResponse          = $this->json('POST', 'api/v1/user/profiles', $profileModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Profile::$responseBody))
//            ->assertJson(array("data"=>$profileModel));
//
//        $profileId     = json_decode($profileResponse->getContent(), true)['data']['id'];
//
//        $this->json('GET', 'api/v1/user/profiles', [], $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=> ['0' => Profile::$responseBody]))
//            ->assertJson(array("data"=> ['0' => $profileModel]));
//
//        $this->cleanupDatabaseOfTestEvent($profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "PROFILE: canGetListOfProfile: PASSED \n";
//    }
//
//    /** @test canNotCreateProfileWithoutCreativeTitle */
//    public function canNotCreateProfileWithoutCreativeTitle()
//    {
//        $profileModel         = factory(Profile::class)->make()->toArray();
//
//        $profileModel['creativeTitle'] = '';
//
//        $this->json('POST', 'api/v1/user/profiles', $profileModel, $this->httpHeader)
//            ->assertStatus(400);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "PROFILE: canNotCreateProfileWithoutCreativeTitle: PASSED \n";
//    }
//
//
//    /** @test canNotCreateProfileWithSameCreativeTitle */
//    public function canNotCreateProfileWithSameCreativeTitle()
//    {
//        $profileModel         = factory(Profile::class)->make()->toArray();
//
//        $profileResponse = $this->json('POST', 'api/v1/user/profiles', $profileModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Profile::$responseBody))
//            ->assertJson(array("data"=>$profileModel));
//
//        $profileId         = json_decode($profileResponse->getContent(), true)['data']['id'];
//        $profileCreativeTitle       = json_decode($profileResponse->getContent(), true)['data']['creativeTitle'];
//
//        $profileModel['creativeTitle'] = $profileCreativeTitle;
//        $this->json('POST', 'api/v1/user/profiles', $profileModel, $this->httpHeader)
//            ->assertStatus(400);
//
//        $this->cleanupDatabaseOfTestEvent($profileId);
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "PROFILE: canNotCreateProfileWithSameCreativeTitle: PASSED \n";
//    }
//
//    /** @test canCreateNewProfile */
//    public function canCreateNewProfile()
//    {
//        $profileModel      = factory(Profile::class)->make()->toArray();
//
//        $profileResponse   = $this->json('POST', 'api/v1/user/profiles', $profileModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Profile::$responseBody))
//            ->assertJson(array("data"=>$profileModel));
//
//        $profileId         = json_decode($profileResponse->getContent(), true)['data']['id'];
//
//        $this->cleanupDatabaseOfTestEvent($profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "PROFILE: canCreateNewProfile: PASSED \n";
//    }
//
//    /** @test canGetSingleProfile */
//    public function canGetSingleProfile()
//    {
//        $profileModel             = factory(Profile::class)->make()->toArray();
//
//        $profileResponse          = $this->json('POST', 'api/v1/user/profiles', $profileModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Profile::$responseBody))
//            ->assertJson(array("data"=>$profileModel));
//
//        $profileId     = json_decode($profileResponse->getContent(), true)['data']['id'];
//
//        $this->json('GET', 'api/v1/user/profiles/'. $profileId, [], $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Profile::$responseBody))
//            ->assertJson(array("data"=>$profileModel));
//
//        $this->cleanupDatabaseOfTestEvent($profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "PROFILE: canGetSingleProfile: PASSED \n";
//    }
//
//    /** @test canNotUpdateProfileWithoutCreativeTitle */
//    public function canNotUpdateProfileWithoutCreativeTitle()
//    {
//        $profileModel             = factory(Profile::class)->make()->toArray();
//
//        $profileResponse          = $this->json('POST', 'api/v1/user/profiles', $profileModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Profile::$responseBody))
//            ->assertJson(array("data"=>$profileModel));
//
//        $profileId     = json_decode($profileResponse->getContent(), true)['data']['id'];
//
//        $profileModel['creativeTitle'] = '';
//
//        $this->json('POST', 'api/v1/user/profiles/update/'. $profileId, $profileModel, $this->httpHeader)
//            ->assertStatus(400);
//
//        $this->cleanupDatabaseOfTestEvent($profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "PROFILE: canNotUpdateProfileWithoutCreativeTitle: PASSED \n";
//    }
//
//    /** @test canNotUpdateProfileWithoutSameCreativeTitle */
//    public function canNotUpdateProfileWithoutSameCreativeTitle()
//    {
//        $profileModel             = factory(Profile::class)->make()->toArray();
//
//        $profileResponse          = $this->json('POST', 'api/v1/user/profiles', $profileModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Profile::$responseBody))
//            ->assertJson(array("data"=>$profileModel));
//
//        $profileId     = json_decode($profileResponse->getContent(), true)['data']['id'];
//        $profileCreativeTitle     = json_decode($profileResponse->getContent(), true)['data']['creativeTitle'];
//
//        $profileModel['creativeTitle'] = $profileCreativeTitle;
//
//        $this->json('POST', 'api/v1/user/profiles/update/'. $profileId, $profileModel, $this->httpHeader)
//            ->assertStatus(200);
//
//        $this->cleanupDatabaseOfTestEvent($profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "PROFILE: canNotUpdateProfileWithoutSameCreativeTitle: PASSED \n";
//    }
//
//    /** @test canUpdateProfile */
//    public function canUpdateProfile()
//    {
//        $profileAModel             = factory(Profile::class)->make()->toArray();
//
//        $profileResponse          = $this->json('POST', 'api/v1/user/profiles', $profileAModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Profile::$responseBody))
//            ->assertJson(array("data"=>$profileAModel));
//
//        $profileId     = json_decode($profileResponse->getContent(), true)['data']['id'];
//
//        $profileBModel             = factory(Profile::class)->make()->toArray();
//
//        $this->json('POST', 'api/v1/user/profiles/update/'. $profileId, $profileBModel, $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Profile::$responseBody))
//            ->assertJson(array("data"=>$profileBModel));
//
//        $this->cleanupDatabaseOfTestEvent($profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "PROFILE: canUpdateProfile: PASSED \n";
//    }
//
//    /** @test canDeleteProfile*/
//    public function canDeleteProfile()
//    {
//        $profileModel             = factory(Profile::class)->make()->toArray();
//
//        $profileResponse          = $this->json('POST', 'api/v1/user/profiles', $profileModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Profile::$responseBody))
//            ->assertJson(array("data"=>$profileModel));
//
//        $profileId     = json_decode($profileResponse->getContent(), true)['data']['id'];
//
//        $this->json('DELETE', 'api/v1/user/profiles/'. $profileId, [], $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Profile::$responseBody));
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "PROFILE: canDeleteProfile: PASSED \n";
//    }
//
//    // Clean up function for profile
//    public function cleanupDatabaseOfTestEvent(int $profileId) {
//        $this->json('DELETE', 'api/v1/user/profiles/'. $profileId, [], $this->httpHeader)->assertStatus(200);
//    }
//
//    // Clean up function for user
//    public function cleanupDatabaseOfTestDataUser() {
//        $this->json('DELETE', 'api/v1/user',[], $this->httpHeader)->assertStatus(410);
//    }
//
//
//}
