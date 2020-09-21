<?php
//
//namespace Tests\Feature;
//
//use App\Collection;
//use App\Exceptions\Handler;
//use App\Group;
//use App\Profile;
//use App\User;
//use Illuminate\Contracts\Debug\ExceptionHandler;
//use Tests\TestCase;
//use Illuminate\Foundation\Testing\WithFaker;
//
//
//class CollectionTest extends TestCase
//{
//    use WithFaker;
//    protected $user;
//    protected $httpHeader;
//    protected $cleanUpAfterTests;
//
//    /*
//     * canGetListOfCollection
//     * canNotCreateCollectionWithoutName
//     * canNotCreateCollectionWithSameName
//     * canCreateNewCollection
//     * canGetSingleCollection
//     * canNotUpdateCollectionWithoutName
//     *  canNotUpdateCollectionWithExistName
//     * canEditCollection
//     * canDeleteCollection
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
//    /** @test canGetListOfCollection*/
//    public function canGetListOfCollection()
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
//            ->assertJsonStructure(array("data"=>Collection::$responseBody));;
//
//        $collectionId     = json_decode($collectionResponse->getContent(), true)['data']['id'];
//
//        $this->json('GET', 'api/v1/user/collections', [], $this->httpHeader)
//            ->assertStatus(200);
//
//
//        $this->cleanupDatabaseOfTestCollection($collectionId, $profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//
//        echo "COLLECTION: canGetListOfCollection: PASSED \n";
//    }
//
//    /** @test canNotCreateCollectionWithoutName */
//    public function canNotCreateCollectionWithoutName()
//    {
//        $collectionModel         = factory(Collection::class)->make()->toArray();
//
//        $collectionModel['name'] = '';
//
//        $profileModel             = factory(Profile::class)->make()->toArray();
//
//        $profileResponse          = $this->json('POST', 'api/v1/user/profiles', $profileModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Profile::$responseBody))
//            ->assertJson(array("data"=>$profileModel));
//
//        $profileId     = json_decode($profileResponse->getContent(), true)['data']['id'];
//
//        $this->json('POST', 'api/v1/user/profiles/'.$profileId.'/collections', $collectionModel, $this->httpHeader)
//            ->assertStatus(400);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "COLLECTION: canNotCreateCollectionWithoutName: PASSED \n";
//    }
//
//    /** @test canNotCreateCollectionWithExistName */
//    public function canNotCreateCollectionWithExistName()
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
//        $collectionModel         = factory(Collection::class)->make()->toArray();
//
//        $collectionResponse = $this->json('POST', 'api/v1/user/profiles/'.$profileId.'/collections', $collectionModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Collection::$responseBody));
//
//        $collectionId           = json_decode($collectionResponse->getContent(), true)['data']['id'];
//        $collectionName         = json_decode($collectionResponse->getContent(), true)['data']['name'];
//
//
//        $collectionModel['name'] = $collectionName;
//        $this->json('POST', 'api/v1/user/profiles/'.$profileId.'/collections', $collectionModel, $this->httpHeader)
//            ->assertStatus(400);
//
//        $this->cleanupDatabaseOfTestCollection($collectionId, $profileId);
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "COLLECTION: canNotCreateCollectionWithExistName: PASSED \n";
//    }
//
//    /** @test canCreateNewCollection */
//    public function canCreateNewCollection()
//    {
//
//        $profileModel             = factory(Profile::class)->make()->toArray();
//
//        $profileResponse          = $this->json('POST', 'api/v1/user/profiles', $profileModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Profile::$responseBody))
//            ->assertJson(array("data"=>$profileModel));
//
//        $profileId     = json_decode($profileResponse->getContent(), true)['data']['id'];
//
//        $collectionModel      = factory(Collection::class)->make()->toArray();
//
//        $collectionResponse   = $this->json('POST', 'api/v1/user/profiles/'.$profileId.'/collections', $collectionModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Collection::$responseBody));
//
//        $collectionId         = json_decode($collectionResponse->getContent(), true)['data']['id'];
//
//        $this->cleanupDatabaseOfTestCollection($collectionId, $profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "COLLECTION: canCreateNewCollection: PASSED \n";
//    }
//
//    /** @test canGetSingleCollection */
//    public function canGetSingleCollection()
//    {
//
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
//        $this->json('GET', 'api/v1/user/profiles/'.$profileId.'/collections/'. $collectionId, [], $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Collection::$responseBody));
//
//        $this->cleanupDatabaseOfTestCollection($collectionId, $profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "COLLECTION: canGetSingleCollection: PASSED \n";
//    }
//
//
//    /** @test canNotUpdateCollectionWithoutName */
//    public function canNotUpdateCollectionWithoutName()
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
//        $collectionModel['name'] = '';
//
//        $this->json('PUT', 'api/v1/user/profiles/'.$profileId.'/collections/'. $collectionId, $collectionModel, $this->httpHeader)
//            ->assertStatus(400);
//
//        $this->cleanupDatabaseOfTestCollection($collectionId, $profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "COLLECTION: canNotUpdateCollectionWithoutName: PASSED \n";
//    }
//
//    /** @test canNotUpdateCollectionWithExistName */
//    public function canNotUpdateCollectionWithExistName()
//    {
//
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
//        $collectionId       = json_decode($collectionResponse->getContent(), true)['data']['id'];
//        $collectionName     = json_decode($collectionResponse->getContent(), true)['data']['name'];
//
//        $collectionModel['name'] = $collectionName;
//
//        $this->json('PUT', 'api/v1/user/profiles/'.$profileId.'/collections/'. $collectionId, $collectionModel, $this->httpHeader)
//            ->assertStatus(400);
//
//        $this->cleanupDatabaseOfTestCollection($collectionId, $profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "COLLECTION: canNotUpdateCollectionWithExistName: PASSED \n";
//    }
//
//    /** @test canUpdateCollection */
//    public function canUpdateCollection()
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
//        $collectionAModel            = factory(Collection::class)->make()->toArray();
//
//        $collectionResponse          = $this->json('POST', 'api/v1/user/profiles/'.$profileId.'/collections', $collectionAModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Collection::$responseBody));
//
//        $collectionId     = json_decode($collectionResponse->getContent(), true)['data']['id'];
//
//        $collectionBModel            = factory(Collection::class)->make()->toArray();
//
//        $this->json('PUT', 'api/v1/user/profiles/'.$profileId.'/collections/'. $collectionId, $collectionBModel, $this->httpHeader)
//
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Collection::$responseBody));
//
//        $this->cleanupDatabaseOfTestCollection($collectionId, $profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "COLLECTION: canUpdateCollection: PASSED \n";
//    }
//
//    /** @test canDeleteCollection */
//    public function canDeleteCollection()
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
//        $this->json('DELETE', 'api/v1/user/profiles/'.$profileId.'/collections/'. $collectionId, [], $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Collection::$responseBody));
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "COLLECTION: canDeleteCollection: PASSED \n";
//    }
//
//    // Clean up function for Collection
//    public function cleanupDatabaseOfTestCollection(int $collectionId, int $profileId) {
//        $this->json('DELETE', 'api/v1/user/profiles/'.$profileId.'/collections/'.$collectionId, [], $this->httpHeader)->assertStatus(200);
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
//}
