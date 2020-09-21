<?php
//
//namespace Tests\Feature;
//
//use App\Exceptions\Handler;
//use App\Profile;
//use App\Event;
//use App\Tag;
//use App\User;
//use Illuminate\Contracts\Debug\ExceptionHandler;
//use Tests\TestCase;
//use Illuminate\Foundation\Testing\WithFaker;
//
//
//class TagEventTest extends TestCase
//{
//    use WithFaker;
//    protected $user;
//    protected $httpHeader;
//    protected $cleanUpAfterTests;
//
//    /*
//     * canGetListOfTagEvent
//     * canNotCreateTagEventWithoutName
//     * canNotCreateTagEventWithSameName
//     * canCreateNewTagEvent
//     * canGetSingleTagEvent
//     * canNotUpdateTagEventWithoutName
//     * canNotUpdateTagEventWithExistName
//     * canUpdateTagEvent
//     * canDeleteTagEvent
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
//    /** @test canGetListOfTagEvent */
//    public function canGetListOfTagEvent()
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
//        $tagModel      = factory(Tag::class)->make()->toArray();
//
//        $tagEventResponse = $this->json('POST', 'api/v1/user/events/'.$eventId.'/tags',  $tagModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Tag::$responseBody))
//            ->assertJson(array("data"=>$tagModel));
//
//        $this->json('GET', 'api/v1/user/events/'.$eventId.'/tags', [], $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>['0' => Tag::$responseBody]))
//            ->assertJson(array("data"=> ['0' => $tagModel]));
//
//        $tagId     = json_decode($tagEventResponse->getContent(), true)['data']['id'];
//
//        $this->cleanupDatabaseOfTestTagEvent($eventId, $tagId);
//
//        $this->cleanupDatabaseOfTestEvent($eventId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "TAG-EVENT: canGetListOfTagEvent: PASSED \n";
//    }
//
//    /** @test canNotCreateTagEventWithoutName */
//    public function canNotCreateTagEventWithoutName()
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
//        $tagModel      = factory(Tag::class)->make()->toArray();
//
//        $tagModel['name'] = '';
//
//        $this->json('POST', 'api/v1/user/events/'.$eventId.'/tags',  $tagModel,  $this->httpHeader)
//            ->assertStatus(400);
//
//        $this->cleanupDatabaseOfTestEvent($eventId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "TAG-EVENT: canNotCreateTagEventWithoutName: PASSED \n";
//    }
//
//    /** @test canNotCreateTagEventWithExistName */
//    public function canNotCreateTagEventWithExistName()
//    {
//        $eventModel         = factory(Event::class)->make()->toArray();
//
//        $eventResponse = $this->json('POST', 'api/v1/user/events', $eventModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Event::$responseBody))
//            ->assertJson(array("data"=>$eventModel));
//
//        $eventId         = json_decode($eventResponse->getContent(), true)['data']['id'];
//
//        $tagModel      = factory(Tag::class)->make()->toArray();
//
//        $tagEventResponse = $this->json('POST', 'api/v1/user/events/'.$eventId.'/tags',  $tagModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Tag::$responseBody))
//            ->assertJson(array("data"=>$tagModel));
//
//        $tagName         = json_decode($tagEventResponse->getContent(), true)['data']['name'];
//        $tagId         = json_decode($tagEventResponse->getContent(), true)['data']['id'];
//
//        $tagModel['name'] = $tagName;
//
//        $this->json('POST', 'api/v1/user/events/'.$eventId.'/tags',  $tagModel,  $this->httpHeader)
//            ->assertStatus(400);
//
//        $this->cleanupDatabaseOfTestTagEvent($eventId, $tagId);
//
//        $this->cleanupDatabaseOfTestEvent($eventId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "TAG-EVENT: canNotCreateTagEventWithExistName: PASSED \n";
//    }
//
//    /** @test canCreateNewTagEvent */
//    public function canCreateNewTagEvent()
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
//        $tagModel      = factory(Tag::class)->make()->toArray();
//
//        $tagEventResponse = $this->json('POST', 'api/v1/user/events/'.$eventId.'/tags',  $tagModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Tag::$responseBody))
//            ->assertJson(array("data"=>$tagModel));
//
//        $tagId         = json_decode($tagEventResponse->getContent(), true)['data']['id'];
//
//        $this->cleanupDatabaseOfTestTagEvent($eventId, $tagId);
//
//        $this->cleanupDatabaseOfTestEvent($eventId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "TAG-EVENT: canCreateNewTagEvent: PASSED \n";
//    }
//
//    /** @test canGetSingleTagEvent */
//    public function canGetSingleTagEvent()
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
//        $tagModel      = factory(Tag::class)->make()->toArray();
//
//        $tagEventResponse = $this->json('POST', 'api/v1/user/events/'.$eventId.'/tags',  $tagModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Tag::$responseBody))
//            ->assertJson(array("data"=>$tagModel));
//
//        $tagId         = json_decode($tagEventResponse->getContent(), true)['data']['id'];
//
//        $this->json('GET', 'api/v1/user/events/'.$eventId.'/tags/'.$tagId, [], $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Tag::$responseBody))
//            ->assertJson(array("data"=>$tagModel));
//
//        $this->cleanupDatabaseOfTestTagEvent($eventId, $tagId);
//
//        $this->cleanupDatabaseOfTestEvent($eventId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "TAG-EVENT: canGetSingleTagEvent: PASSED \n";
//    }
//
//    /** @test canNotUpdateTagEventWithoutName */
//    public function canNotUpdateTagEventWithoutName()
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
//        $tagModel      = factory(Tag::class)->make()->toArray();
//
//        $tagEventResponse = $this->json('POST', 'api/v1/user/events/'.$eventId.'/tags',  $tagModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Tag::$responseBody))
//            ->assertJson(array("data"=>$tagModel));
//
//        $tagId         = json_decode($tagEventResponse->getContent(), true)['data']['id'];
//
//        $tagModel      = factory(Tag::class)->make()->toArray();
//
//        $tagModel['name'] = '';
//
//        $this->json('PUT', 'api/v1/user/events/'.$eventId.'/tags/'. $tagId, $tagModel, $this->httpHeader)
//            ->assertStatus(400);
//
//        $this->cleanupDatabaseOfTestTagEvent($eventId, $tagId);
//
//        $this->cleanupDatabaseOfTestEvent($eventId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "TAG-EVENT: canNotUpdateTagEventWithoutName: PASSED \n";
//    }
//
//    /** @test canNotUpdateTagEventWithExistName */
//    public function canNotUpdateTagEventWithExistName()
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
//        $tagModel      = factory(Tag::class)->make()->toArray();
//
//        $tagEventResponse = $this->json('POST', 'api/v1/user/events/'.$eventId.'/tags',  $tagModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Tag::$responseBody))
//            ->assertJson(array("data"=>$tagModel));
//
//        $tagId         = json_decode($tagEventResponse->getContent(), true)['data']['id'];
//
//        $tagName         = json_decode($tagEventResponse->getContent(), true)['data']['name'];
//
//        $tagModel      = factory(Tag::class)->make()->toArray();
//
//        $tagModel['name'] = $tagName;
//
//        $this->json('PUT', 'api/v1/user/events/'.$eventId.'/tags/'. $tagId, $tagModel, $this->httpHeader)
//            ->assertStatus(400);
//
//        $this->cleanupDatabaseOfTestTagEvent($eventId, $tagId);
//
//        $this->cleanupDatabaseOfTestEvent($eventId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "TAG-EVENT: canNotUpdateTagEventWithExistName: PASSED \n";
//    }
//
//    /** @test canUpdateTagEvent */
//    public function canUpdateTagEvent()
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
//        $tagAModel      = factory(Tag::class)->make()->toArray();
//
//        $tagEventResponse = $this->json('POST', 'api/v1/user/events/'.$eventId.'/tags',  $tagAModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Tag::$responseBody))
//            ->assertJson(array("data"=>$tagAModel));
//
//        $tagId         = json_decode($tagEventResponse->getContent(), true)['data']['id'];
//
//        $tagBModel      = factory(Tag::class)->make()->toArray();
//
//        $this->json('PUT', 'api/v1/user/events/'.$eventId.'/tags/'. $tagId, $tagBModel, $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Tag::$responseBody))
//            ->assertJson(array("data"=>$tagBModel));
//
//        $this->cleanupDatabaseOfTestTagEvent($eventId, $tagId);
//
//        $this->cleanupDatabaseOfTestEvent($eventId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "TAG-EVENT: canUpdateTagEvent: PASSED \n";
//    }
//
//    /** @test canDeleteTagEvent */
//    public function canDeleteTagEvent()
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
//        $tagModel      = factory(Tag::class)->make()->toArray();
//
//        $tagEventResponse = $this->json('POST', 'api/v1/user/events/'.$eventId.'/tags',  $tagModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Tag::$responseBody))
//            ->assertJson(array("data"=>$tagModel));
//
//        $tagId         = json_decode($tagEventResponse->getContent(), true)['data']['id'];
//
//        $this->json('DELETE', 'api/v1/user/events/'.$eventId.'/tags/'. $tagId, [], $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Tag::$responseBody))
//            ->assertJson(array("data"=>$tagModel));
//
//        $this->cleanupDatabaseOfTestEvent($eventId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "TAG-EVENT: canDeleteTagEvent: PASSED \n";
//    }
//
//    // Clean up function for tag event
//    public function cleanupDatabaseOfTestTagEvent(int $eventId, int $tagId) {
//        $this->json('DELETE', 'api/v1/user/events/'.$eventId.'/tags/'.$tagId, [], $this->httpHeader)->assertStatus(200);
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
