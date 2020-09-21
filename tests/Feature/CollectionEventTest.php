<?php
//
//namespace Tests\Feature;
//
//use App\Collection;
//use App\Event;
//use App\Exceptions\Handler;
//use App\Profile;
//use App\User;
//use Illuminate\Contracts\Debug\ExceptionHandler;
//use Tests\TestCase;
//use Illuminate\Foundation\Testing\WithFaker;
//
//
//class CollectionEventTest extends TestCase
//{
//    use WithFaker;
//    protected $user;
//    protected $httpHeader;
//    /*
//     * canGetListOfCollectionEvent
//     * canCreateNewCollectionEvent
//     * canGetSingleCollectionEvent
//     * canDeleteCollectionEvent
//     */
//
//    protected $cleanUpAfterTests;
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
//    /** @test canGetListOfCollectionEvent*/
//    public function canGetListOfCollectionEvent()
//    {
//        $profileModel             = factory(Profile::class)->make()->toArray();
//
//        $profileResponse          = $this->json('POST', 'api/v1/user/profiles', $profileModel, $this->httpHeader)
//           ->assertStatus(201)
//           ->assertJsonStructure(array("data"=>Profile::$responseBody))
//           ->assertJson(array("data"=>$profileModel));
//
//        $profileId     = json_decode($profileResponse->getContent(), true)['data']['id'];
//
//        $collectionModel             = factory(Collection::class)->make()->toArray();
//
//        $collectionResponse          = $this->json('POST', 'api/v1/user/profiles/'.$profileId.'/collections', $collectionModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Collection::$responseBody));
//
//        $collectionId     = json_decode($collectionResponse->getContent(), true)['data']['id'];
//
//        $eventModel             = factory(Event::class)->make()->toArray();
//
//
//
//        $eventResponse          = $this->json('POST', 'api/v1/user/profiles/'.$profileId.'/events', $eventModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Event::$responseBody));
//
//        $eventId     = json_decode($eventResponse->getContent(), true)['data']['id'];
//
//
//        $this->json('POST', 'api/v1/user/collections/'.$collectionId.'/events/'.$eventId, [], $this->httpHeader)
//            ->assertStatus(201)
//           ->assertJsonStructure(array("data"=>Event::$responseBody));
//
//
//        $this->json('GET', 'api/v1/user/collections/'.$collectionId.'/events', [], $this->httpHeader)
//            ->assertStatus(200);
//
//        $this->cleanupDatabaseOfTestCollectionEvent($collectionId, $eventId);
//
//        $this->cleanupDatabaseOfTestCollection($collectionId, $profileId);
//
//        $this->cleanupDatabaseOfTestEvent($eventId, $profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "COLLECTION-EVENT: canGetListOfCollectionEvent: PASSED \n";
//    }
//
//
//
//    /** @test canCreateNewCollectionEvent */
//    public function canCreateNewCollectionEvent()
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
//        $collectionModel             = factory(Collection::class)->make()->toArray();
//
//        $collectionResponse          = $this->json('POST', 'api/v1/user/profiles/'.$profileId.'/collections', $collectionModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Collection::$responseBody));
//
//        $collectionId     = json_decode($collectionResponse->getContent(), true)['data']['id'];
//
//        $eventModel             = factory(Event::class)->make()->toArray();
//
//        $eventResponse          = $this->json('POST', 'api/v1/user/profiles/'.$profileId.'/events', $eventModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Event::$responseBody));
//
//        $eventId     = json_decode($eventResponse->getContent(), true)['data']['id'];
//
//        $this->json('POST', 'api/v1/user/collections/'.$collectionId.'/events/'.$eventId, [], $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Event::$responseBody));
//
//        $this->cleanupDatabaseOfTestCollectionEvent($collectionId, $eventId);
//
//        $this->cleanupDatabaseOfTestCollection($collectionId, $profileId);
//
//        $this->cleanupDatabaseOfTestEvent($eventId, $profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "COLLECTION-EVENT: canCreateNewCollectionEvent: PASSED \n";
//    }
//
//
//    /** @test canGetSingleCollectionEventcanGetSingleCollectionEvent */
//    public function canGetSingleCollectionEvent()
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
//        $collectionModel             = factory(Collection::class)->make()->toArray();
//
//        $collectionResponse          = $this->json('POST', 'api/v1/user/profiles/'.$profileId.'/collections', $collectionModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Collection::$responseBody));
//
//        $collectionId     = json_decode($collectionResponse->getContent(), true)['data']['id'];
//
//        $eventModel             = factory(Event::class)->make()->toArray();
//
//        $eventResponse          = $this->json('POST', 'api/v1/user/profiles/'.$profileId.'/events', $eventModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Event::$responseBody));
//
//        $eventId     = json_decode($eventResponse->getContent(), true)['data']['id'];
//
//        $this->json('POST', 'api/v1/user/collections/'.$collectionId.'/events/'.$eventId, [], $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Event::$responseBody));
//
//        $this->json('GET', 'api/v1/user/collections/'.$collectionId.'/events/'.$eventId, [], $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Event::$responseBody));
//
//        $this->cleanupDatabaseOfTestCollectionEvent($collectionId, $eventId);
//
//        $this->cleanupDatabaseOfTestCollection($collectionId, $profileId);
//
//        $this->cleanupDatabaseOfTestEvent($eventId, $profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "COLLECTION-EVENT: canGetSingleCollectionEvent: PASSED \n";
//    }
//
//    /** @test canDeleteCollectionEvent */
//    public function canDeleteCollectionEvent ()
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
//
//        $collectionModel             = factory(Collection::class)->make()->toArray();
//
//        $collectionResponse          = $this->json('POST', 'api/v1/user/profiles/'.$profileId.'/collections', $collectionModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Collection::$responseBody));
//
//
//        $collectionId     = json_decode($collectionResponse->getContent(), true)['data']['id'];
//
//        $eventModel             = factory(Event::class)->make()->toArray();
//
//        $eventResponse          = $this->json('POST', 'api/v1/user/profiles/'.$profileId.'/events', $eventModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Event::$responseBody));
//
//        $eventId     = json_decode($eventResponse->getContent(), true)['data']['id'];
//
//        $this->json('POST', 'api/v1/user/collections/'.$collectionId.'/events/'.$eventId, [], $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Event::$responseBody));
//
//        $this->json('DELETE', 'api/v1/user/collections/'.$collectionId.'/events/'. $eventId, [], $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Event::$responseBody));
//
//
//        $this->cleanupDatabaseOfTestCollection($collectionId, $profileId);
//
//        $this->cleanupDatabaseOfTestEvent($eventId, $profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "COLLECTION-EVENT: canDeleteCollectionEvent: PASSED \n";
//    }
//
//    // Clean up function for Collection
//    public function cleanupDatabaseOfTestCollection(int $collectionId, int $profileId) {
//        $this->json('DELETE', 'api/v1/user/profiles/'.$profileId.'/collections/'.$collectionId, [], $this->httpHeader)
//            ->assertStatus(200);
//    }
//
//    // Clean up function for event
//    public function cleanupDatabaseOfTestEvent(int $eventId, int $profileId) {
//        $this->json('DELETE', 'api/v1/user/profiles/'.$profileId.'/events/'. $eventId, [], $this->httpHeader)
//            ->assertStatus(200);
//    }
//
//    // Clean up function for event from collection
//    public function cleanupDatabaseOfTestCollectionEvent(int $collectionId, int $eventId) {
//        $this->json('DELETE', 'api/v1/user/collections/'.$collectionId.'/events/'. $eventId, [], $this->httpHeader)
//            ->assertStatus(200);
//
//    }
//
//    // Clean up function for user
//    public function cleanupDatabaseOfTestDataUser() {
//        $this->json('DELETE', 'api/v1/user',[], $this->httpHeader)
//            ->assertStatus(410);
//    }
//
//
//}
