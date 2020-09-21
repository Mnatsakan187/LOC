<?php
//
//namespace Tests\Feature;
//
//use App\Exceptions\Handler;
//use App\Poll;
//use App\Profile;
//use App\User;
//use Illuminate\Contracts\Debug\ExceptionHandler;
//use Tests\TestCase;
//use Illuminate\Foundation\Testing\WithFaker;
//
//
//class PollTest extends TestCase
//{
//    use WithFaker;
//    protected $user;
//    protected $httpHeader;
//    protected $cleanUpAfterTests;
//
//    /*
//     * canGetListOfPoll
//     * canNotCreatePollWithoutQuestion
//     * canNotCreatePollWithSameName
//     * canCreateNewPoll
//     * canGetSinglePoll
//     * canNotUpdatePollWithoutQuestion
//     * canUpdatePoll
//     * canDeletePoll
//     * cannotCreatePoll
//     * cannotUpdatePoll
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
//    /** @test canGetListOfPoll */
//    public function canGetListOfPoll()
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
//        $pollModel             = factory(Poll::class)->make()->toArray();
//
//        $pollResponse          = $this->json('POST', 'api/v1/user/profiles/'.$profileId.'/polls', $pollModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Poll::$responseBody));
//
//        $pollId     = json_decode($pollResponse->getContent(), true)['data']['id'];
//
//        $this->json('GET', 'api/v1/user/polls', [], $this->httpHeader)
//            ->assertStatus(200);
//
//        $this->cleanupDatabaseOfTestPoll($pollId, $profileId);
//
//        $this->cleanupDatabaseOfTestProfile($profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "POLL: canGetListOfPoll: PASSED \n";
//    }
//
//    /** @test canNotCreatePollWithoutQuestion */
//    public function canNotCreatePollWithoutQuestion()
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
//        $pollModel         = factory(Poll::class)->make()->toArray();
//
//        $pollModel['question'] = '';
//
//        $this->json('POST', 'api/v1/user/profiles/'.$profileId.'/polls', $pollModel, $this->httpHeader)
//            ->assertStatus(400);
//
//        $this->cleanupDatabaseOfTestProfile($profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "POLL: canNotCreatePollWithoutQuestion: PASSED \n";
//    }
//
//
//    /** @test canNotCreatePollWithExistQuestion */
//    public function canNotCreatePollWithExistQuestion()
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
//
//        $pollModel         = factory(Poll::class)->make()->toArray();
//
//        $pollResponse = $this->json('POST', 'api/v1/user/profiles/'.$profileId.'/polls', $pollModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Poll::$responseBody));
//
//        $pollId         = json_decode($pollResponse->getContent(), true)['data']['id'];
//        $pollQuestion       = json_decode($pollResponse->getContent(), true)['data']['question'];
//
//        $pollModel['question'] = $pollQuestion;
//        $this->json('POST', 'api/v1/user/profiles/'.$profileId.'/polls', $pollModel, $this->httpHeader)
//            ->assertStatus(400);
//
//        $this->cleanupDatabaseOfTestPoll($pollId, $profileId);
//
//        $this->cleanupDatabaseOfTestProfile($profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "POLL: canNotCreatePollWithExistQuestion: PASSED \n";
//    }
//
//
//
//    /** @test canCreateNewPoll */
//    public function canCreateNewPoll()
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
//        $pollModel      = factory(Poll::class)->make()->toArray();
//
//        $pollResponse   = $this->json('POST', 'api/v1/user/profiles/'.$profileId.'/polls', $pollModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Poll::$responseBody));
//
//
//        $pollId         = json_decode($pollResponse->getContent(), true)['data']['id'];
//
//        $this->cleanupDatabaseOfTestPoll($pollId, $profileId);
//
//        $this->cleanupDatabaseOfTestProfile($profileId);
//
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "POLL: canCreateNewPoll: PASSED \n";
//    }
//
//    /** @test canGetSinglePoll */
//    public function canGetSinglePoll()
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
//        $pollModel             = factory(Poll::class)->make()->toArray();
//
//        $pollResponse          = $this->json('POST', 'api/v1/user/profiles/'.$profileId.'/polls', $pollModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Poll::$responseBody));
//
//        $pollId     = json_decode($pollResponse->getContent(), true)['data']['id'];
//
//        $this->json('GET', 'api/v1/user/profiles/'.$profileId.'/polls/'. $pollId, [], $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Poll::$responseBody));
//
//        $this->cleanupDatabaseOfTestPoll($pollId, $profileId);
//
//        $this->cleanupDatabaseOfTestProfile($profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "POLL: canGetSinglePoll: PASSED \n";
//    }
//
//
//    /** @test canNotUpdatePollWithoutQuestion */
//    public function canNotUpdatePollWithoutQuestion()
//    {
//
//        $profileModel      = factory(Profile::class)->make()->toArray();
//
//        $profileResponse   = $this->json('POST', 'api/v1/user/profiles', $profileModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Profile::$responseBody))
//            ->assertJson(array("data"=>$profileModel));
//
//        $profileId         = json_decode($profileResponse->getContent(), true)['data']['id'];
//
//        $pollModel             = factory(Poll::class)->make()->toArray();
//
//        $pollResponse          = $this->json('POST', 'api/v1/user/profiles/'.$profileId.'/polls', $pollModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Poll::$responseBody));
//
//        $pollId     = json_decode($pollResponse->getContent(), true)['data']['id'];
//
//        $pollModel['question'] = '';
//
//        $this->json('PUT', 'api/v1/user/profiles/'.$profileId.'/polls/'. $pollId, $pollModel, $this->httpHeader)
//            ->assertStatus(400);
//
//        $this->cleanupDatabaseOfTestPoll($pollId, $profileId);
//
//        $this->cleanupDatabaseOfTestProfile($profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "POLL: canNotUpdatePollWithoutQuestion: PASSED \n";
//    }
//
//    /** @test canNotUpdatePollWithExistQuestion */
//    public function canNotUpdatePollWithExistQuestion()
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
//        $pollModel             = factory(Poll::class)->make()->toArray();
//
//        $pollResponse          = $this->json('POST', 'api/v1/user/profiles/'.$profileId.'/polls', $pollModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Poll::$responseBody));
//
//
//        $pollId     = json_decode($pollResponse->getContent(), true)['data']['id'];
//        $pollQuestion     = json_decode($pollResponse->getContent(), true)['data']['question'];
//
//        $pollModel['question'] = $pollQuestion;
//
//        $this->json('PUT', 'api/v1/user/profiles/'.$profileId.'/polls/'. $pollId, $pollModel, $this->httpHeader)
//            ->assertStatus(200);
//
//        $this->cleanupDatabaseOfTestPoll($pollId, $profileId);
//
//        $this->cleanupDatabaseOfTestProfile($profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "POLL: canNotUpdatePollWithExistQuestion: PASSED \n";
//    }
//
//    /** @test canUpdatePoll */
//    public function canUpdatePoll()
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
//        $pollAModel             = factory(Poll::class)->make()->toArray();
//
//        $pollResponse          = $this->json('POST', 'api/v1/user/profiles/'.$profileId.'/polls', $pollAModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Poll::$responseBody));
//
//        $pollId     = json_decode($pollResponse->getContent(), true)['data']['id'];
//
//        $pollBModel             = factory(Poll::class)->make()->toArray();
//
//        $this->json('PUT', 'api/v1/user/profiles/'.$profileId.'/polls/'. $pollId, $pollBModel, $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Poll::$responseBody));
//
//        $this->cleanupDatabaseOfTestPoll($pollId, $profileId);
//
//        $this->cleanupDatabaseOfTestProfile($profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "POLL: canUpdatePoll: PASSED \n";
//    }
//
//    /** @test canDeletePoll */
//    public function canDeletePoll()
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
//        $pollModel             = factory(Poll::class)->make()->toArray();
//
//        $pollResponse          = $this->json('POST', 'api/v1/user/profiles/'.$profileId.'/polls', $pollModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Poll::$responseBody));
//
//        $pollId     = json_decode($pollResponse->getContent(), true)['data']['id'];
//
//
//        $this->json('DELETE', 'api/v1/user/profiles/'.$profileId.'/polls/'. $pollId, [], $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Poll::$responseBody));
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "POLL: canDeletePoll: PASSED \n";
//    }
//
//    // Clean up function for poll
//    public function cleanupDatabaseOfTestPoll(int $pollId, int $profileId) {
//        $this->json('DELETE', 'api/v1/user/profiles/'.$profileId.'/polls/'. $pollId, [], $this->httpHeader)->assertStatus(200);
//    }
//
//    // Clean up function for user
//    public function cleanupDatabaseOfTestDataUser() {
//        $this->json('DELETE', 'api/v1/user',[], $this->httpHeader)->assertStatus(410);
//    }
//
//    // Clean up function for profile
//    public function cleanupDatabaseOfTestProfile(int $profileId) {
//        $this->json('DELETE', 'api/v1/user/profiles/'. $profileId, [], $this->httpHeader)->assertStatus(200);
//    }
//
//
//}
