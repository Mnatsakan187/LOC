<?php
//
//namespace Tests\Feature;
//
//use App\Collection;
//
//use App\Exceptions\Handler;
//use App\Profile;
//use App\User;
//use Illuminate\Contracts\Debug\ExceptionHandler;
//use Tests\TestCase;
//use Illuminate\Foundation\Testing\WithFaker;
//
//
//class CollectionCreatorTest extends TestCase
//{
//    use WithFaker;
//    protected $user;
//    protected $httpHeader;
//    /*
//     * canGetListOfCollectionCreator
//     * canCreateNewCollectionCreator
//     * canGetSingleCollectionCreator
//     * canDeleteCollectionCreator
//     */
//
//    protected $cleanUpAfterTests;
//
//    protected $creatorId;
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
//        $this->creatorId = $userId;
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
//    /** @test canGetListOfCollectionCreator*/
//    public function canGetListOfCollectionCreator()
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
//
//        $this->json('POST', 'api/v1/user/collections/'.$collectionId.'/creators/'.$this->creatorId, [], $this->httpHeader)
//            ->assertStatus(201);
//
//
//
//
//        $this->json('GET', 'api/v1/user/collections/'.$collectionId.'/creators', [], $this->httpHeader)
//            ->assertStatus(200);
//
//        $this->cleanupDatabaseOfTestCollectionCreator($collectionId, $this->creatorId);
//
//        $this->cleanupDatabaseOfTestCollection($collectionId, $profileId);
//
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "COLLECTION-CREATOR: canGetListOfCollectionCreator: PASSED \n";
//    }
//
//
//
//    /** @test canCreateNewCollectionCreator */
//    public function canCreateNewCollectionCreator()
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
//
//        $this->json('POST', 'api/v1/user/collections/'.$collectionId.'/creators/'.$this->creatorId, [], $this->httpHeader)
//            ->assertStatus(201);
//
//        $this->cleanupDatabaseOfTestCollectionCreator($collectionId, $this->creatorId);
//
//        $this->cleanupDatabaseOfTestCollection($collectionId, $profileId);
//
//
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "COLLECTION-CREATOR: canCreateNewCollectionCreator: PASSED \n";
//    }
//
//
//    /** @test canGetSingleCollectionCreatorcanGetSingleCollectionCreator */
//    public function canGetSingleCollectionCreator()
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
//
//
//        $this->json('POST', 'api/v1/user/collections/'.$collectionId.'/creators/'.$this->creatorId, [], $this->httpHeader)
//            ->assertStatus(201);
//
//        $this->json('GET', 'api/v1/user/collections/'.$collectionId.'/creators/'.$this->creatorId, [], $this->httpHeader)
//            ->assertStatus(200);
//
//        $this->cleanupDatabaseOfTestCollectionCreator($collectionId, $this->creatorId);
//
//        $this->cleanupDatabaseOfTestCollection($collectionId, $profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "COLLECTION-CREATOR: canGetSingleCollectionCreator: PASSED \n";
//    }
//
//    /** @test canDeleteCollectionCreator */
//    public function canDeleteCollectionCreator ()
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
//
//        $this->json('POST', 'api/v1/user/collections/'.$collectionId.'/creators/'.$this->creatorId, [], $this->httpHeader)
//            ->assertStatus(201);
//
//        $this->json('DELETE', 'api/v1/user/collections/'.$collectionId.'/creators/'. $this->creatorId, [], $this->httpHeader)
//            ->assertStatus(200);
//
//
//        $this->cleanupDatabaseOfTestCollection($collectionId, $profileId);
//
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "COLLECTION-CREATOR: canDeleteCollectionCreator: PASSED \n";
//    }
//
//    // Clean up function for Collection
//    public function cleanupDatabaseOfTestCollection(int $collectionId, int $profileId) {
//        $this->json('DELETE', 'api/v1/user/profiles/'.$profileId.'/collections/'.$collectionId, [], $this->httpHeader)
//            ->assertStatus(200);
//    }
//
////    // Clean up function for creator
////    public function cleanupDatabaseOfTestCreator(int $creatorId, int $profileId) {
////        $this->json('DELETE', 'api/v1/user/profiles/'.$profileId.'/creators/'. $creatorId, [], $this->httpHeader)
////            ->assertStatus(200);
////    }
//
//    // Clean up function for creator from collection
//    public function cleanupDatabaseOfTestCollectionCreator(int $collectionId, int $creatorId) {
//        $this->json('DELETE', 'api/v1/user/collections/'.$collectionId.'/creators/'. $creatorId, [], $this->httpHeader)
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
