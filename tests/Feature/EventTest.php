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
//class EventTest extends TestCase
//{
//    use WithFaker;
//    protected $user;
//    protected $httpHeader;
//    protected $cleanUpAfterTests;
//
//    /*
//     * canGetListOfEvent
//     * canNotCreateEventWithoutName
//     * canNotCreateEventWithSameName
//     * canCreateNewEvent
//     * canGetSingleEvent
//     * canNotUpdateEventWithoutName
//     * canNotUpdateEventWithoutSameName
//     * canUpdateEvent
//     * canDeleteEvent
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
//    /** @test canGetListOfEvent */
//    public function canGetListOfEvent()
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
//        $this->json('GET', 'api/v1/user/events', [], $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=> ['0' => Event::$responseBody]))
//            ->assertJson(array("data"=> ['0' => $eventModel]));
//
//        $this->cleanupDatabaseOfTestEvent($eventId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "EVENT: canGetListOfEvent: PASSED \n";
//    }
//
//    /** @test canNotCreateEventWithoutName */
//    public function canNotCreateEventWithoutName()
//    {
//        $eventModel         = factory(Event::class)->make()->toArray();
//
//        $eventModel['name'] = '';
//
//        $this->json('POST', 'api/v1/user/events', $eventModel, $this->httpHeader)
//            ->assertStatus(400);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "EVENT: cannotCreateEventWithoutName: PASSED \n";
//    }
//
//
//    /** @test canNotCreateEventWithExistName */
//    public function canNotCreateEventWithExistName()
//    {
//        $eventModel         = factory(Event::class)->make()->toArray();
//
//        $eventResponse = $this->json('POST', 'api/v1/user/events', $eventModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Event::$responseBody))
//            ->assertJson(array("data"=>$eventModel));
//
//        $eventId         = json_decode($eventResponse->getContent(), true)['data']['id'];
//        $eventName       = json_decode($eventResponse->getContent(), true)['data']['name'];
//
//        $eventModel['name'] = $eventName;
//        $this->json('POST', 'api/v1/user/events', $eventModel, $this->httpHeader)
//            ->assertStatus(400);
//
//        $this->cleanupDatabaseOfTestEvent($eventId);
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "EVENT: canNotCreateEventWithExistName: PASSED \n";
//    }
//
//
//
//    /** @test canCreateNewEvent */
//    public function canCreateNewEvent()
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
//        $this->cleanupDatabaseOfTestEvent($eventId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "EVENT: canCreateNewEvent: PASSED \n";
//    }
//
//    /** @test canGetSingleEvent */
//    public function canGetSingleEvent()
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
//        $this->json('GET', 'api/v1/user/events/'. $eventId, [], $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Event::$responseBody))
//            ->assertJson(array("data"=>$eventModel));
//
//        $this->cleanupDatabaseOfTestEvent($eventId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "EVENT: canGetSingleEvent: PASSED \n";
//    }
//
//
//    /** @test canNotUpdateEventWithoutName */
//    public function canNotUpdateEventWithoutName()
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
//        $eventModel['name'] = '';
//
//        $this->json('PUT', 'api/v1/user/events/'. $eventId, $eventModel, $this->httpHeader)
//            ->assertStatus(400);
//
//        $this->cleanupDatabaseOfTestEvent($eventId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "EVENT: canNotUpdateEventWithoutName: PASSED \n";
//    }
//
//    /** @test canNotUpdateEventWithExistName */
//    public function canNotUpdateEventWithExistName()
//    {
//        $eventModel             = factory(Event::class)->make()->toArray();
//
//        $eventResponse          = $this->json('POST', 'api/v1/user/events', $eventModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Event::$responseBody))
//            ->assertJson(array("data"=>$eventModel));
//
//        $eventId     = json_decode($eventResponse->getContent(), true)['data']['id'];
//        $eventName     = json_decode($eventResponse->getContent(), true)['data']['name'];
//
//        $eventModel['name'] = $eventName;
//
//        $this->json('PUT', 'api/v1/user/events/'. $eventId, $eventModel, $this->httpHeader)
//            ->assertStatus(400);
//
//        $this->cleanupDatabaseOfTestEvent($eventId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "EVENT: canNotUpdateEventWithExistName: PASSED \n";
//    }
//
//    /** @test canUpdateEvent */
//    public function canUpdateEvent()
//    {
//        $eventAModel             = factory(Event::class)->make()->toArray();
//
//        $eventResponse          = $this->json('POST', 'api/v1/user/events', $eventAModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Event::$responseBody))
//            ->assertJson(array("data"=>$eventAModel));
//
//        $eventId     = json_decode($eventResponse->getContent(), true)['data']['id'];
//
//        $eventBModel             = factory(Event::class)->make()->toArray();
//
//        $this->json('PUT', 'api/v1/user/events/'. $eventId, $eventBModel, $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Event::$responseBody))
//            ->assertJson(array("data"=>$eventBModel));
//
//        $this->cleanupDatabaseOfTestEvent($eventId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "EVENT: canUpdateEvent: PASSED \n";
//    }
//
//    /** @test canDeleteEvent*/
//    public function canDeleteEvent()
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
//        $this->json('DELETE', 'api/v1/user/events/'. $eventId, [], $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Event::$responseBody));
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "EVENT: canDeleteEvent: PASSED \n";
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
