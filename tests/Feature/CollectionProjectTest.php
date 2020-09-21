<?php
//
//namespace Tests\Feature;
//
//use App\Collection;
//use App\Exceptions\Handler;
//use App\Group;
//use App\Profile;
//use App\Project;
//use App\User;
//use Illuminate\Contracts\Debug\ExceptionHandler;
//use Tests\TestCase;
//use Illuminate\Foundation\Testing\WithFaker;
//
//
//class CollectionProjectTest extends TestCase
//{
//    use WithFaker;
//    protected $user;
//    protected $httpHeader;
//    /*
//     * canGetListOfCollectionProject
//     * canNotCreateCollectionProjectWithSameProjectId
//     * canCreateNewCollectionProject
//     * canGetSingleCollectionProject
//     * canDeleteCollectionProject
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
//    /** @test canGetListOfCollectionProject*/
//    public function canGetListOfCollectionProject()
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
//        $projectModel             = factory(Project::class)->make()->toArray();
//
//
//
//        $projectResponse          = $this->json('POST', 'api/v1/user/profiles/'.$profileId.'/projects', $projectModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Project::$responseBody));
//
//        $projectId     = json_decode($projectResponse->getContent(), true)['data']['id'];
//
//
//        $this->json('POST', 'api/v1/user/collections/'.$collectionId.'/projects/'.$projectId, [], $this->httpHeader)
//            ->assertStatus(201)
//           ->assertJsonStructure(array("data"=>Project::$responseBody));
//
//
//        $this->json('GET', 'api/v1/user/collections/'.$collectionId.'/projects', [], $this->httpHeader)
//            ->assertStatus(200);
//
//        $this->cleanupDatabaseOfTestCollectionProject($collectionId, $projectId);
//
//        $this->cleanupDatabaseOfTestCollection($collectionId, $profileId);
//
//        $this->cleanupDatabaseOfTestProject($projectId, $profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "COLLECTION-PROJECT: canGetListOfCollectionProject: PASSED \n";
//    }
//
//
//
//    /** @test canCreateNewCollectionProject */
//    public function canCreateNewCollectionProject()
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
//        $projectModel             = factory(Project::class)->make()->toArray();
//
//        $projectResponse          = $this->json('POST', 'api/v1/user/profiles/'.$profileId.'/projects', $projectModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Project::$responseBody));
//
//        $projectId     = json_decode($projectResponse->getContent(), true)['data']['id'];
//
//        $this->json('POST', 'api/v1/user/collections/'.$collectionId.'/projects/'.$projectId, [], $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Project::$responseBody));
//
//        $this->cleanupDatabaseOfTestCollectionProject($collectionId, $projectId);
//
//        $this->cleanupDatabaseOfTestCollection($collectionId, $profileId);
//
//        $this->cleanupDatabaseOfTestProject($projectId, $profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "COLLECTION-PROJECT: canCreateNewCollectionProject: PASSED \n";
//    }
//
//
//    /** @test canGetSingleCollectionProject */
//    public function canGetSingleCollectionProject()
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
//        $projectModel             = factory(Project::class)->make()->toArray();
//
//        $projectResponse          = $this->json('POST', 'api/v1/user/profiles/'.$profileId.'/projects', $projectModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Project::$responseBody));
//
//        $projectId     = json_decode($projectResponse->getContent(), true)['data']['id'];
//
//        $this->json('POST', 'api/v1/user/collections/'.$collectionId.'/projects/'.$projectId, [], $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Project::$responseBody));
//
//        $this->json('GET', 'api/v1/user/collections/'.$collectionId.'/projects/'.$projectId, [], $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Project::$responseBody));
//
//        $this->cleanupDatabaseOfTestCollectionProject($collectionId, $projectId);
//
//        $this->cleanupDatabaseOfTestCollection($collectionId, $profileId);
//
//        $this->cleanupDatabaseOfTestProject($projectId, $profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "COLLECTION-PROJECT: canGetSingleCollectionProject: PASSED \n";
//    }
//
//    /** @test canDeleteCollectionProject */
//    public function canDeleteCollectionProject ()
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
//        $projectModel             = factory(Project::class)->make()->toArray();
//
//        $projectResponse          = $this->json('POST', 'api/v1/user/profiles/'.$profileId.'/projects', $projectModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Project::$responseBody));
//
//        $projectId     = json_decode($projectResponse->getContent(), true)['data']['id'];
//
//        $this->json('POST', 'api/v1/user/collections/'.$collectionId.'/projects/'.$projectId, [], $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Project::$responseBody));
//
//        $this->json('DELETE', 'api/v1/user/collections/'.$collectionId.'/projects/'. $projectId, [], $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Project::$responseBody));
//
//
//        $this->cleanupDatabaseOfTestCollection($collectionId, $profileId);
//
//        $this->cleanupDatabaseOfTestProject($projectId, $profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "COLLECTION-PROJECT: canDeleteCollectionProject: PASSED \n";
//    }
//
//    // Clean up function for Collection
//    public function cleanupDatabaseOfTestCollection(int $collectionId, int $profileId) {
//        $this->json('DELETE', 'api/v1/user/profiles/'.$profileId.'/collections/'.$collectionId, [], $this->httpHeader)
//            ->assertStatus(200);
//    }
//
//    // Clean up function for project
//    public function cleanupDatabaseOfTestProject(int $projectId, int $profileId) {
//        $this->json('DELETE', 'api/v1/user/profiles/'.$profileId.'/projects/'. $projectId, [], $this->httpHeader)
//            ->assertStatus(200);
//    }
//
//    // Clean up function for project from collection
//    public function cleanupDatabaseOfTestCollectionProject(int $collectionId, int $projectId) {
//        $this->json('DELETE', 'api/v1/user/collections/'.$collectionId.'/projects/'. $projectId, [], $this->httpHeader)
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
