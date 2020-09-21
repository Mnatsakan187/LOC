<?php
//
//namespace Tests\Feature;
//
//use App\Exceptions\Handler;
//use App\Event;
//use App\Like;
//use App\User;
//use Illuminate\Contracts\Debug\ExceptionHandler;
//use Tests\TestCase;
//use Illuminate\Foundation\Testing\WithFaker;
//
//
//class LikeEventTest extends TestCase
//{
//    use WithFaker;
//    protected $user;
//    protected $httpHeader;
//    protected $cleanUpAfterTests;
//
//    /*
//     * canGetListOfLikeEvent
//     * cannotCreateLikeEvent
//     * canCreateNewLikeEvent
//     * canGetSingleLikeEvent
//     * canDeleteLikeEvent
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
//    /** @test canGetListOfLikeEvent */
//    public function canGetListOfLikeEvent()
//    {
//        $eventModel             = factory(Event::class)->make()->toArray();
//
//        $eventResponse          = $this->json('POST', 'api/v1/user/events', $eventModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Event::$responseBody))
//            ->assertJson(array("data"=>$eventModel));
//
//        $eventId     = json_decode($eventResponse->getContent(), true)['data']['id'];
//
//        $likeModel      = factory(Like::class)->make()->toArray();
//
//        $likeEventResponse = $this->json('POST', 'api/v1/user/events/'.$eventId.'/likes',  $likeModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Like::$responseBody))
//            ->assertJson(array("data"=>$likeModel));
//
//        $this->json('GET', 'api/v1/user/events/'.$eventId.'/likes', [], $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>['0' => Like::$responseBody]))
//            ->assertJson(array("data"=>['0' => $likeModel]));
//
//        $likeId     = json_decode($likeEventResponse->getContent(), true)['data']['id'];
//
//        $this->cleanupDatabaseOfTestLikeEvent($eventId, $likeId);
//
//        $this->cleanupDatabaseOfTestEvent($eventId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "LIKE-EVENT: canGetListOfLikeEvent: PASSED \n";
//    }
//
//    /** @test canNotCreateLikeEventWithoutLikeData */
//    public function canNotCreateLikeEventWithoutLikeData()
//    {
//        $eventModel             = factory(Event::class)->make()->toArray();
//
//        $eventResponse          = $this->json('POST', 'api/v1/user/events', $eventModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Event::$responseBody))
//            ->assertJson(array("data"=>$eventModel));
//
//        $eventId     = json_decode($eventResponse->getContent(), true)['data']['id'];
//
//        $likeModel      = factory(Like::class)->make()->toArray();
//
//        $likeModel['likedDate'] = '';
//
//        $this->json('POST', 'api/v1/user/events/'.$eventId.'/likes',  $likeModel,  $this->httpHeader)
//            ->assertStatus(400);
//
//        $this->cleanupDatabaseOfTestEvent($eventId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "LIKE-EVENT: canNotCreateLikeEventWithoutLikeData: PASSED \n";
//    }
//
//    /** @test canCreateNewLikeEvent */
//    public function canCreateNewLikeEvent()
//    {
//        $eventModel      = factory(Event::class)->make()->toArray();
//
//        $eventResponse   = $this->json('POST', 'api/v1/user/events', $eventModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Event::$responseBody))
//            ->assertJson(array("data"=>$eventModel));
//
//        $eventId         = json_decode($eventResponse->getContent(), true)['data']['id'];
//
//        $likeModel      = factory(Like::class)->make()->toArray();
//
//        $likeEventResponse = $this->json('POST', 'api/v1/user/events/'.$eventId.'/likes',  $likeModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Like::$responseBody))
//            ->assertJson(array("data"=>$likeModel));
//
//        $likeId         = json_decode($likeEventResponse->getContent(), true)['data']['id'];
//
//        $this->cleanupDatabaseOfTestLikeEvent($eventId, $likeId);
//
//        $this->cleanupDatabaseOfTestEvent($eventId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "LIKE-EVENT: canCreateNewLikeEvent: PASSED \n";
//    }
//
//    /** @test canGetSingleLikeEvent */
//    public function canGetSingleLikeEvent()
//    {
//        $eventModel             = factory(Event::class)->make()->toArray();
//
//        $eventResponse          = $this->json('POST', 'api/v1/user/events', $eventModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Event::$responseBody))
//            ->assertJson(array("data"=>$eventModel));
//
//        $eventId     = json_decode($eventResponse->getContent(), true)['data']['id'];
//
//        $likeModel      = factory(Like::class)->make()->toArray();
//
//        $likeEventResponse = $this->json('POST', 'api/v1/user/events/'.$eventId.'/likes',  $likeModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Like::$responseBody))
//            ->assertJson(array("data"=>$likeModel));
//
//        $likeId         = json_decode($likeEventResponse->getContent(), true)['data']['id'];
//
//        $this->json('GET', 'api/v1/user/events/'.$eventId.'/likes/'.$likeId, [], $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Like::$responseBody))
//            ->assertJson(array("data"=>$likeModel));
//
//        $this->cleanupDatabaseOfTestLikeEvent($eventId, $likeId);
//
//        $this->cleanupDatabaseOfTestEvent($eventId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "LIKE-EVENT: canGetSingleLikeEvent: PASSED \n";
//    }
//
//    /** @test canDeleteLikeEvent */
//    public function canDeleteLikeEvent()
//    {
//        $eventModel      = factory(Event::class)->make()->toArray();
//
//        $eventResponse   = $this->json('POST', 'api/v1/user/events', $eventModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Event::$responseBody))
//            ->assertJson(array("data"=>$eventModel));
//
//        $eventId         = json_decode($eventResponse->getContent(), true)['data']['id'];
//
//        $likeModel      = factory(Like::class)->make()->toArray();
//
//        $likeEventResponse = $this->json('POST', 'api/v1/user/events/'.$eventId.'/likes',  $likeModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Like::$responseBody))
//            ->assertJson(array("data"=>$likeModel));
//
//        $likeId         = json_decode($likeEventResponse->getContent(), true)['data']['id'];
//
//        $this->json('DELETE', 'api/v1/user/events/'.$eventId.'/likes/'. $likeId, [], $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Like::$responseBody))
//            ->assertJson(array("data"=>$likeModel));
//
//        $this->cleanupDatabaseOfTestEvent($eventId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "LIKE-EVENT: canDeleteLikeEvent: PASSED \n";
//    }
//
//    // Clean up function for like event
//    public function cleanupDatabaseOfTestLikeEvent(int $eventId, int $likeId) {
//        $this->json('DELETE', 'api/v1/user/events/'.$eventId.'/likes/'.$likeId, [], $this->httpHeader)->assertStatus(200);
//    }
//
//    // Clean up function for event
//    public function cleanupDatabaseOfTestEvent(int $eventId) {
//        $this->json('DELETE', 'api/v1/user/events/'. $eventId, [], $this->httpHeader)->assertStatus(200);
//    }
//
//    // Clean up function for user
//    public function cleanupDatabaseOfTestDataUser() {
//        $this->json('DELETE', 'api/v1/user',[], $this->httpHeader)->assertStatus(410);
//    }
//
//
//}
