<?php
//
//namespace Tests\Feature;
//
//use App\Comment;
//use App\Event;
//use App\Exceptions\Handler;
//use App\Profile;
//use App\User;
//use Illuminate\Contracts\Debug\ExceptionHandler;
//use Tests\TestCase;
//use Illuminate\Foundation\Testing\WithFaker;
//
//
//class CommentEventTest extends TestCase
//{
//    use WithFaker;
//    protected $user;
//    protected $httpHeader;
//    protected $cleanUpAfterTests;
//
//    /*
//     * canGetListOfCommentEvent
//     * canNotCreateCommentEventWithoutName
//     * canNotCreateCommentEventWithSameName
//     * canCreateNewCommentEvent
//     * canGetSingleCommentEvent
//     * canNotUpdateCommentEventtWithoutName
//     * canUpdateCommentEvent
//     * canDeleteCommentEvent
//     * cannotCreateCommentEvent
//     * cannotUpdateCommentEvent
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
//            'password'  => 123456,
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
//    /** @test canGetListOfCommentEvent */
//    public function canGetListOfCommentEvent()
//    {
//        $eventModel             = factory(Event::class)->make()->toArray();
//
//        $profileModel      = factory(Profile::class)->make()->toArray();
//
//        $profileResponse   = $this->json('POST', 'api/v1/user/profiles', $profileModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Profile::$responseBody))
//            ->assertJson(array("data"=>$profileModel));
//
//
//        $profileId         = json_decode($profileResponse->getContent(), true)['data']['id'];
//
//        $eventResponse          = $this->json('POST', 'api/v1/user/events', $eventModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Event::$responseBody));
//
//        $eventId     = json_decode($eventResponse->getContent(), true)['data']['id'];
//
//
//        $commentModel      = factory(Comment::class)->make()->toArray();
//
//        $commentEventResponse = $this->json('POST', 'api/v1/user/events/'.$eventId.'/comments',  $commentModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Comment::$responseBody))
//            ->assertJson(array("data"=>$commentModel));
//
//        $this->json('GET', 'api/v1/user/events/'.$eventId.'/comments', [], $this->httpHeader)
//            ->assertStatus(200);
//
//        $commentId     = json_decode($commentEventResponse->getContent(), true)['data']['id'];
//
//        $this->cleanupDatabaseOfTestCommentEvent($eventId, $commentId);
//
//        $this->cleanupDatabaseOfTestEvent($eventId, $profileId);
//
//        $this->cleanupDatabaseOfTestProfile($profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "COMMENT-EVENT: canGetListOfCommentEvent: PASSED \n";
//    }
//
//    /** @test canNotCreateCommentEventWithoutName */
//    public function canNotCreateCommentEventWithoutName()
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
//        $eventModel             = factory(Event::class)->make()->toArray();
//
//        $eventResponse          = $this->json('POST', 'api/v1/user/events', $eventModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Event::$responseBody));
//
//        $eventId     = json_decode($eventResponse->getContent(), true)['data']['id'];
//
//        $commentModel      = factory(Comment::class)->make()->toArray();
//
//        $commentModel['commentText'] = '';
//
//        $this->json('POST', 'api/v1/user/events/'.$eventId.'/comments',  $commentModel,  $this->httpHeader)
//            ->assertStatus(400);
//
//        $this->cleanupDatabaseOfTestEvent($eventId, $profileId);
//
//        $this->cleanupDatabaseOfTestProfile($profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "COMMENT-EVENT: canNotCreateCommentEventWithoutName: PASSED \n";
//    }
//
//
//
//
//    /** @test canCreateNewCommentEvent */
//    public function canCreateNewCommentEvent()
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
//        $eventModel      = factory(Event::class)->make()->toArray();
//
//        $eventResponse   = $this->json('POST', 'api/v1/user/events', $eventModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Event::$responseBody));
//
//        $eventId         = json_decode($eventResponse->getContent(), true)['data']['id'];
//
//        $commentModel      = factory(Comment::class)->make()->toArray();
//
//        $commentEventResponse = $this->json('POST', 'api/v1/user/events/'.$eventId.'/comments',  $commentModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Comment::$responseBody))
//            ->assertJson(array("data"=>$commentModel));
//
//        $commentId         = json_decode($commentEventResponse->getContent(), true)['data']['id'];
//
//        $this->cleanupDatabaseOfTestCommentEvent($eventId, $commentId);
//
//        $this->cleanupDatabaseOfTestEvent($eventId, $profileId);
//
//        $this->cleanupDatabaseOfTestProfile($profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "COMMENT-EVENT: canCreateNewCommentEvent: PASSED \n";
//    }
//
//    /** @test canGetSingleCommentEvent */
//    public function canGetSingleCommentEvent()
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
//        $eventModel             = factory(Event::class)->make()->toArray();
//
//        $eventResponse          = $this->json('POST', 'api/v1/user/events', $eventModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Event::$responseBody));
//
//        $eventId     = json_decode($eventResponse->getContent(), true)['data']['id'];
//
//
//        $commentModel      = factory(Comment::class)->make()->toArray();
//
//        $commentEventResponse = $this->json('POST', 'api/v1/user/events/'.$eventId.'/comments',  $commentModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Comment::$responseBody))
//            ->assertJson(array("data"=>$commentModel));
//
//        $commentId         = json_decode($commentEventResponse->getContent(), true)['data']['id'];
//
//        $this->json('GET', 'api/v1/user/events/'.$eventId.'/comments/'.$commentId, [], $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Comment::$responseBody))
//            ->assertJson(array("data"=>$commentModel));
//
//        $this->cleanupDatabaseOfTestCommentEvent($eventId, $commentId);
//
//        $this->cleanupDatabaseOfTestEvent($eventId, $profileId);
//
//        $this->cleanupDatabaseOfTestProfile($profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "COMMENT-EVENT: canGetSingleCommentEvent: PASSED \n";
//    }
//
//    /** @test canNotUpdateCommentEventtWithoutName */
//    public function canNotUpdateCommentEventtWithoutName()
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
//        $eventModel             = factory(Event::class)->make()->toArray();
//
//        $eventResponse          = $this->json('POST', 'api/v1/user/events', $eventModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Event::$responseBody));
//
//
//        $eventId     = json_decode($eventResponse->getContent(), true)['data']['id'];
//
//        $commentModel      = factory(Comment::class)->make()->toArray();
//
//        $commentEventResponse = $this->json('POST', 'api/v1/user/events/'.$eventId.'/comments',  $commentModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Comment::$responseBody))
//            ->assertJson(array("data"=>$commentModel));
//
//        $commentId         = json_decode($commentEventResponse->getContent(), true)['data']['id'];
//
//        $commentModel      = factory(Comment::class)->make()->toArray();
//
//        $commentModel['commentText'] = '';
//
//        $this->json('PUT', 'api/v1/user/events/'.$eventId.'/comments/'. $commentId, $commentModel, $this->httpHeader)
//            ->assertStatus(400);
//
//        $this->cleanupDatabaseOfTestCommentEvent($eventId, $commentId);
//
//        $this->cleanupDatabaseOfTestEvent($eventId, $profileId);
//
//        $this->cleanupDatabaseOfTestProfile($profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "COMMENT-EVENT: canNotUpdateCommentEventtWithoutName: PASSED \n";
//    }
//
//
//    /** @test canUpdateCommentEvent */
//    public function canUpdateCommentEvent()
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
//        $eventModel             = factory(Event::class)->make()->toArray();
//
//        $eventResponse          = $this->json('POST', 'api/v1/user/events', $eventModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Event::$responseBody));
//
//        $eventId     = json_decode($eventResponse->getContent(), true)['data']['id'];
//
//        $commentAModel      = factory(Comment::class)->make()->toArray();
//
//        $commentEventResponse = $this->json('POST', 'api/v1/user/events/'.$eventId.'/comments',  $commentAModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Comment::$responseBody))
//            ->assertJson(array("data"=>$commentAModel));
//
//        $commentId         = json_decode($commentEventResponse->getContent(), true)['data']['id'];
//
//        $commentBModel      = factory(Comment::class)->make()->toArray();
//
//        $this->json('PUT', 'api/v1/user/events/'.$eventId.'/comments/'. $commentId, $commentBModel, $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Comment::$responseBody))
//            ->assertJson(array("data"=>$commentBModel));
//
//        $this->cleanupDatabaseOfTestCommentEvent($eventId, $commentId);
//
//        $this->cleanupDatabaseOfTestEvent($eventId, $profileId);
//
//        $this->cleanupDatabaseOfTestProfile($profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "COMMENT-EVENT: canUpdateCommentEvent: PASSED \n";
//    }
//
//    /** @test canDeleteCommentEvent */
//    public function canDeleteCommentEvent()
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
//        $eventModel      = factory(Event::class)->make()->toArray();
//
//        $eventResponse   = $this->json('POST', 'api/v1/user/events', $eventModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Event::$responseBody));
//
//        $eventId         = json_decode($eventResponse->getContent(), true)['data']['id'];
//
//        $commentModel      = factory(Comment::class)->make()->toArray();
//
//        $commentEventResponse = $this->json('POST', 'api/v1/user/events/'.$eventId.'/comments',  $commentModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Comment::$responseBody))
//            ->assertJson(array("data"=>$commentModel));
//
//        $commentId         = json_decode($commentEventResponse->getContent(), true)['data']['id'];
//
//        $this->json('DELETE', 'api/v1/user/events/'.$eventId.'/comments/'. $commentId, [], $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Comment::$responseBody))
//            ->assertJson(array("data"=>$commentModel));
//
//        $this->cleanupDatabaseOfTestEvent($eventId, $profileId);
//
//        $this->cleanupDatabaseOfTestProfile($profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "COMMENT-EVENT: canDeleteCommentEvent: PASSED \n";
//    }
//
//    // Clean up function for comment event
//    public function cleanupDatabaseOfTestCommentEvent(int $eventId, int $commentId) {
//        $this->json('DELETE', 'api/v1/user/events/'.$eventId.'/comments/'.$commentId, [], $this->httpHeader)->assertStatus(200);
//    }
//
//    // Clean up function for event
//    public function cleanupDatabaseOfTestEvent(int $eventId, int $profileId) {
//        $this->json('DELETE', 'api/v1/user/events/'. $eventId, [], $this->httpHeader)->assertStatus(200);
//    }
//
//    // Clean up function for user
//    public function cleanupDatabaseOfTestDataUser() {
//        $this->json('DELETE', 'api/v1/user',[], $this->httpHeader)->assertStatus(410);
//    }
//
//
//    // Clean up function for profile
//    public function cleanupDatabaseOfTestProfile(int $profileId) {
//        $this->json('DELETE', 'api/v1/user/profiles/'. $profileId, [], $this->httpHeader)->assertStatus(200);
//    }
//
//
//}
