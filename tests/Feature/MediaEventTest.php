<?php
//
//namespace Tests\Feature;
//
//use App\Exceptions\Handler;
//use App\Event;
//use App\Media;
//use App\User;
//use Illuminate\Contracts\Debug\ExceptionHandler;
//use Tests\TestCase;
//use Illuminate\Foundation\Testing\WithFaker;
//
//
//class MediaEventTest extends TestCase
//{
//    use WithFaker;
//    protected $user;
//    protected $httpHeader;
//    protected $cleanUpAfterTests;
//
//    /*
//     * canGetListOfMediaEvent
//     * canNotCreateMediaEventWithoutName
//     * canNotCreateMediaEventWithExistName
//     * canCreateMediaEvent
//     * canGetSingleMediaEvent
//     * canNotUpdateMediaEventWithoutName
//     * canNotUpdateMediaEventWithExistName
//     * canUpdateMediaEvent
//     * canDeleteMediaEvent
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
//    /** @test canGetListOfMediaEvent */
//    public function canGetListOfMediaEvent()
//    {
//        $eventModel             = factory(Event::class)->make()->toArray();
//
//        $eventResponse          = $this->json('POST', 'api/v1/user/events', $eventModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Event::$responseBody))
//            ->assertJson(array("data"=>$eventModel));
//
//        $eventId      = json_decode($eventResponse->getContent(), true)['data']['id'];
//
//        $mediaModel   = factory(Media::class)->make()->toArray();
//
//        $mediaEventResponse = $this->json('POST', 'api/v1/user/events/'.$eventId.'/media',  $mediaModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Media::$responseBody))
//            ->assertJson(array("data"=>$mediaModel));
//
//        $this->json('GET', 'api/v1/user/events/'.$eventId.'/media', [], $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data" => ['0' => Media::$responseBody]))
//            ->assertJson(array("data"=>['0' => $mediaModel]));
//
//        $mediaId     = json_decode($mediaEventResponse->getContent(), true)['data']['id'];
//
//        $this->cleanupDatabaseOfTestMediaEvent($eventId, $mediaId);
//
//        $this->cleanupDatabaseOfTestEvent($eventId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "MEDIA-EVENT: canGetListOfMediaEvent: PASSED \n";
//    }
//
//    /** @test canNotCreateMediaEventWithoutName */
//    public function canNotCreateMediaEventWithoutName()
//    {
//        $eventModel        = factory(Event::class)->make()->toArray();
//
//        $eventResponse     = $this->json('POST', 'api/v1/user/events', $eventModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Event::$responseBody))
//            ->assertJson(array("data"=>$eventModel));
//
//        $eventId     = json_decode($eventResponse->getContent(), true)['data']['id'];
//
//        $mediaModel  = factory(Media::class)->make()->toArray();
//
//        $mediaModel['displayName'] = '';
//
//        $this->json('POST', 'api/v1/user/events/'.$eventId.'/media',  $mediaModel,  $this->httpHeader)
//            ->assertStatus(400);
//
//        $this->cleanupDatabaseOfTestEvent($eventId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "MEDIA-EVENT: canNotCreateMediaEventWithoutName: PASSED \n";
//    }
//
//
//    /** @test canCreateMediaEvent */
//    public function canCreateMediaEvent()
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
//        $mediaModel      = factory(Media::class)->make()->toArray();
//
//        $mediaEventResponse = $this->json('POST', 'api/v1/user/events/'.$eventId.'/media',  $mediaModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Media::$responseBody))
//            ->assertJson(array("data"=>$mediaModel));
//
//        $mediaId         = json_decode($mediaEventResponse->getContent(), true)['data']['id'];
//
//        $this->cleanupDatabaseOfTestMediaEvent($eventId, $mediaId);
//
//        $this->cleanupDatabaseOfTestEvent($eventId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "MEDIA-EVENT: canCreateMediaEvent: PASSED \n";
//    }
//
//    /** @test canGetSingleMediaEvent */
//    public function canGetSingleMediaEvent()
//    {
//        $eventModel       = factory(Event::class)->make()->toArray();
//
//        $eventResponse    = $this->json('POST', 'api/v1/user/events', $eventModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Event::$responseBody))
//            ->assertJson(array("data"=>$eventModel));
//
//        $eventId       = json_decode($eventResponse->getContent(), true)['data']['id'];
//
//        $mediaModel    = factory(Media::class)->make()->toArray();
//
//        $mediaEventResponse = $this->json('POST', 'api/v1/user/events/'.$eventId.'/media',  $mediaModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Media::$responseBody))
//            ->assertJson(array("data"=>$mediaModel));
//
//        $mediaId         = json_decode($mediaEventResponse->getContent(), true)['data']['id'];
//
//        $this->json('GET', 'api/v1/user/events/'.$eventId.'/media/'.$mediaId, [], $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Media::$responseBody))
//            ->assertJson(array("data"=>$mediaModel));
//
//        $this->cleanupDatabaseOfTestMediaEvent($eventId, $mediaId);
//
//        $this->cleanupDatabaseOfTestEvent($eventId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "MEDIA-EVENT: canGetSingleMediaEvent: PASSED \n";
//    }
//
//    /** @test canNotUpdateMediaEventWithoutName */
//    public function canNotUpdateMediaEventWithoutName()
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
//        $mediaModel      = factory(Media::class)->make()->toArray();
//
//        $mediaEventResponse = $this->json('POST', 'api/v1/user/events/'.$eventId.'/media',  $mediaModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Media::$responseBody))
//            ->assertJson(array("data"=>$mediaModel));
//
//        $mediaId         = json_decode($mediaEventResponse->getContent(), true)['data']['id'];
//
//        $mediaModel      = factory(Media::class)->make()->toArray();
//
//        $mediaModel['displayName'] = '';
//
//        $this->json('PUT', 'api/v1/user/events/'.$eventId.'/media/'. $mediaId, $mediaModel, $this->httpHeader)
//            ->assertStatus(400);
//
//        $this->cleanupDatabaseOfTestMediaEvent($eventId, $mediaId);
//
//        $this->cleanupDatabaseOfTestEvent($eventId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "MEDIA-EVENT: canNotUpdateMediaEventWithoutName: PASSED \n";
//    }
//
//
//
//    /** @test canUpdateMediaEvent */
//    public function canUpdateMediaEvent()
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
//        $mediaAModel      = factory(Media::class)->make()->toArray();
//
//        $mediaEventResponse = $this->json('POST', 'api/v1/user/events/'.$eventId.'/media',  $mediaAModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Media::$responseBody))
//            ->assertJson(array("data"=>$mediaAModel));
//
//        $mediaId         = json_decode($mediaEventResponse->getContent(), true)['data']['id'];
//
//        $mediaBModel      = factory(Media::class)->make()->toArray();
//
//        $this->json('PUT', 'api/v1/user/events/'.$eventId.'/media/'. $mediaId, $mediaBModel, $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Media::$responseBody))
//            ->assertJson(array("data"=>$mediaBModel));
//
//        $this->cleanupDatabaseOfTestMediaEvent($eventId, $mediaId);
//
//        $this->cleanupDatabaseOfTestEvent($eventId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "MEDIA-EVENT: canUpdateMediaEvent: PASSED \n";
//    }
//
//    /** @test canDeleteMediaEvent */
//    public function canDeleteMediaEvent()
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
//        $mediaModel      = factory(Media::class)->make()->toArray();
//
//        $mediaEventResponse = $this->json('POST', 'api/v1/user/events/'.$eventId.'/media',  $mediaModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Media::$responseBody))
//            ->assertJson(array("data"=>$mediaModel));
//
//        $mediaId         = json_decode($mediaEventResponse->getContent(), true)['data']['id'];
//
//        $this->json('DELETE', 'api/v1/user/events/'.$eventId.'/media/'. $mediaId, [], $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Media::$responseBody))
//            ->assertJson(array("data"=>$mediaModel));
//
//        $this->cleanupDatabaseOfTestEvent($eventId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "MEDIA-EVENT: canDeleteMediaEvent: PASSED \n";
//    }
//
//    // Clean up function for media event
//    public function cleanupDatabaseOfTestMediaEvent(int $eventId, int $mediaId) {
//        $this->json('DELETE', 'api/v1/user/events/'.$eventId.'/media/'.$mediaId, [], $this->httpHeader)->assertStatus(200);
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
