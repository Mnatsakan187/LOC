<?php
//
//namespace Tests\Feature;
//
//use App\Event;
//use App\Exceptions\Handler;
//use App\User;
//use Illuminate\Contracts\Debug\ExceptionHandler;
//use Tests\TestCase;
//use Illuminate\Foundation\Testing\WithFaker;
//
//
//class FollowTest extends TestCase
//{
//    use WithFaker;
//    protected $user;
//    protected $httpHeader;
//    protected $cleanUpAfterTests;
//    protected $userId;
//
//    /*
//     * canGetListOfFollow
//     * canNotCreateFollowWithoutName
//     * canNotCreateFollowWithSameName
//     * canCreateNewFollow
//     * canGetSingleFollow
//     * canDeleteFollow
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
//        $this->userId = $user->id;
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
//    /** @test canGetListOfFollow */
//    public function canGetListOfFollow()
//    {
//        $this->json('GET', 'api/v1/user/follows/'. $this->userId , [], $this->httpHeader)
//            ->assertStatus(200);
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "FOLLOW:canGetListOfFollow: PASSED \n";
//    }
//
//    /** @test canNotCreateFollowWithoutFollowId */
//    public function canNotCreateFollowFollowId()
//    {
//        $this->json('POST', 'api/v1/user/follows/'. "sa" , [], $this->httpHeader)
//            ->assertStatus(400);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "FOLLOW:canNotCreateFollowFollowId: PASSED \n";
//    }
//
//
//
//    /** @test canCreateNewFollow */
//    public function canCreateNewFollow()
//    {
//        $followResponse =  $this->json('POST', 'api/v1/user/follows/'. $this->userId , [], $this->httpHeader)
//            ->assertStatus(201);
//
//        $followId         = json_decode($followResponse->getContent(), true)['data']['id'];
//
//        $this->cleanupDatabaseOfFollow($followId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "FOLLOW:canCreateNewFollow: PASSED \n";
//    }
//
//    /** @test canGetSingleFollow */
//    public function canGetSingleFollow()
//    {
//        $this->json('GET', 'api/v1/user/follows/'. $this->userId , [], $this->httpHeader)
//            ->assertStatus(200);
//
//        $this->cleanupDatabaseOfTestDataUser();
//        echo "FOLLOW:canGetSingleFollow: PASSED \n";
//    }
//
//
//
//    /** @test canDeleteFollow*/
//    public function canDeleteFollow()
//    {
//        $this->json('GET', 'api/v1/user/follows/'. $this->userId , [], $this->httpHeader)
//            ->assertStatus(200);
//
//        $this->cleanupDatabaseOfTestDataUser();
//        echo "FOLLOW:canDeleteFollow: PASSED \n";
//    }
//
//    // Clean up function for event
//    public function cleanupDatabaseOfFollow(int $followId) {
//        $this->json('DELETE', 'api/v1/user/follows/'. $followId, [], $this->httpHeader)->assertStatus(200);
//    }
//
//    // Clean up function for user
//    public function cleanupDatabaseOfTestDataUser() {
//        $this->json('DELETE', 'api/v1/user',[], $this->httpHeader)->assertStatus(410);
//    }
//
//
//}
