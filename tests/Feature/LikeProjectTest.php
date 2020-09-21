<?php
//
//namespace Tests\Feature;
//
//use App\Exceptions\Handler;
//use App\Project;
//use App\Like;
//use App\User;
//use Illuminate\Contracts\Debug\ExceptionHandler;
//use Tests\TestCase;
//use Illuminate\Foundation\Testing\WithFaker;
//
//
//class LikeProjectTest extends TestCase
//{
//    use WithFaker;
//    protected $user;
//    protected $httpHeader;
//    protected $cleanUpAfterTests;
//
//    /*
//     * canGetListOfLikeProject
//     * cannotCreateLikeProject
//     * canCreateNewLikeProject
//     * canGetSingleLikeProject
//     * canDeleteLikeProject
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
//    /** @test canGetListOfLikeProject */
//    public function canGetListOfLikeProject()
//    {
//        $projectModel             = factory(Project::class)->make()->toArray();
//
//        $projectResponse          = $this->json('POST', 'api/v1/user/projects', $projectModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Project::$responseBody))
//            ->assertJson(array("data"=>$projectModel));
//
//        $projectId     = json_decode($projectResponse->getContent(), true)['data']['id'];
//
//        $likeModel      = factory(Like::class)->make()->toArray();
//
//        $likeProjectResponse = $this->json('POST', 'api/v1/user/projects/'.$projectId.'/likes',  $likeModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Like::$responseBody))
//            ->assertJson(array("data"=>$likeModel));
//
//        $this->json('GET', 'api/v1/user/projects/'.$projectId.'/likes', [], $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>['0' => Like::$responseBody]))
//            ->assertJson(array("data"=>['0' => $likeModel]));
//
//        $likeId     = json_decode($likeProjectResponse->getContent(), true)['data']['id'];
//
//        $this->cleanupDatabaseOfTestLikeProject($projectId, $likeId);
//
//        $this->cleanupDatabaseOfTestProject($projectId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "LIKE-PROJECT: canGetListOfLikeProject: PASSED \n";
//    }
//
//    /** @test canNotCreateLikeProjectWithoutLikeData */
//    public function canNotCreateLikeProjectWithoutLikeData()
//    {
//        $projectModel             = factory(Project::class)->make()->toArray();
//
//        $projectResponse          = $this->json('POST', 'api/v1/user/projects', $projectModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Project::$responseBody))
//            ->assertJson(array("data"=>$projectModel));
//
//        $projectId     = json_decode($projectResponse->getContent(), true)['data']['id'];
//
//        $likeModel      = factory(Like::class)->make()->toArray();
//
//        $likeModel['likedDate'] = '';
//
//        $this->json('POST', 'api/v1/user/projects/'.$projectId.'/likes',  $likeModel,  $this->httpHeader)
//            ->assertStatus(400);
//
//        $this->cleanupDatabaseOfTestProject($projectId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "LIKE-PROJECT: canNotCreateLikeProjectWithoutLikeData: PASSED \n";
//    }
//
//    /** @test canCreateNewLikeProject */
//    public function canCreateNewLikeProject()
//    {
//        $projectModel      = factory(Project::class)->make()->toArray();
//
//        $projectResponse   = $this->json('POST', 'api/v1/user/projects', $projectModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Project::$responseBody))
//            ->assertJson(array("data"=>$projectModel));
//
//        $projectId         = json_decode($projectResponse->getContent(), true)['data']['id'];
//
//        $likeModel      = factory(Like::class)->make()->toArray();
//
//        $likeProjectResponse = $this->json('POST', 'api/v1/user/projects/'.$projectId.'/likes',  $likeModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Like::$responseBody))
//            ->assertJson(array("data"=>$likeModel));
//
//        $likeId         = json_decode($likeProjectResponse->getContent(), true)['data']['id'];
//
//        $this->cleanupDatabaseOfTestLikeProject($projectId, $likeId);
//
//        $this->cleanupDatabaseOfTestProject($projectId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "LIKE-PROJECT: canCreateNewLikeProject: PASSED \n";
//    }
//
//    /** @test canGetSingleLikeProject */
//    public function canGetSingleLikeProject()
//    {
//        $projectModel             = factory(Project::class)->make()->toArray();
//
//        $projectResponse          = $this->json('POST', 'api/v1/user/projects', $projectModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Project::$responseBody))
//            ->assertJson(array("data"=>$projectModel));
//
//        $projectId     = json_decode($projectResponse->getContent(), true)['data']['id'];
//
//        $likeModel      = factory(Like::class)->make()->toArray();
//
//        $likeProjectResponse = $this->json('POST', 'api/v1/user/projects/'.$projectId.'/likes',  $likeModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Like::$responseBody))
//            ->assertJson(array("data"=>$likeModel));
//
//        $likeId         = json_decode($likeProjectResponse->getContent(), true)['data']['id'];
//
//        $this->json('GET', 'api/v1/user/projects/'.$projectId.'/likes/'.$likeId, [], $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Like::$responseBody))
//            ->assertJson(array("data"=>$likeModel));
//
//        $this->cleanupDatabaseOfTestLikeProject($projectId, $likeId);
//
//        $this->cleanupDatabaseOfTestProject($projectId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "LIKE-PROJECT: canGetSingleLikeProject: PASSED \n";
//    }
//
//    /** @test canDeleteLikeProject */
//    public function canDeleteLikeProject()
//    {
//        $projectModel      = factory(Project::class)->make()->toArray();
//
//        $projectResponse   = $this->json('POST', 'api/v1/user/projects', $projectModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Project::$responseBody))
//            ->assertJson(array("data"=>$projectModel));
//
//        $projectId         = json_decode($projectResponse->getContent(), true)['data']['id'];
//
//        $likeModel      = factory(Like::class)->make()->toArray();
//
//        $likeProjectResponse = $this->json('POST', 'api/v1/user/projects/'.$projectId.'/likes',  $likeModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Like::$responseBody))
//            ->assertJson(array("data"=>$likeModel));
//
//        $likeId         = json_decode($likeProjectResponse->getContent(), true)['data']['id'];
//
//        $this->json('DELETE', 'api/v1/user/projects/'.$projectId.'/likes/'. $likeId, [], $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Like::$responseBody))
//            ->assertJson(array("data"=>$likeModel));
//
//        $this->cleanupDatabaseOfTestProject($projectId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "LIKE-PROJECT: canDeleteLikeProject: PASSED \n";
//    }
//
//    // Clean up function for like project
//    public function cleanupDatabaseOfTestLikeProject(int $projectId, int $likeId) {
//        $this->json('DELETE', 'api/v1/user/projects/'.$projectId.'/likes/'.$likeId, [], $this->httpHeader)->assertStatus(200);
//    }
//
//    // Clean up function for project
//    public function cleanupDatabaseOfTestProject(int $projectId) {
//        $this->json('DELETE', 'api/v1/user/projects/'. $projectId, [], $this->httpHeader)->assertStatus(200);
//    }
//
//    // Clean up function for user
//    public function cleanupDatabaseOfTestDataUser() {
//        $this->json('DELETE', 'api/v1/user',[], $this->httpHeader)->assertStatus(410);
//    }
//
//
//}
