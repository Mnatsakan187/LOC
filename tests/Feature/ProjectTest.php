<?php
//
//namespace Tests\Feature;
//
//use App\Exceptions\Handler;
//use App\Profile;
//use App\Project;
//use App\User;
//use Illuminate\Contracts\Debug\ExceptionHandler;
//use Tests\TestCase;
//use Illuminate\Foundation\Testing\WithFaker;
//
//
//class ProjectTest extends TestCase
//{
//    use WithFaker;
//    protected $user;
//    protected $httpHeader;
//    protected $cleanUpAfterTests;
//
//    /*
//     * canGetListOfProject
//     * canNotCreateProjectWithoutName
//     * canNotCreateProjectWithSameName
//     * canCreateNewProject
//     * canGetSingleProject
//     * canNotUpdateProjectWithoutName
//     * canUpdateProject
//     * canDeleteProject
//     * cannotCreateProject with existing name for same user
//     * cannotUpdateProject name to one that already exists for that user
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
//    /** @test canGetListOfProject */
//    public function canGetListOfProject()
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
//        $projectModel             = factory(Project::class)->make()->toArray();
//
//        $projectResponse          = $this->json('POST', 'api/v1/user/profiles/'.$profileId.'/projects', $projectModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Project::$responseBody));
//
//        $projectId     = json_decode($projectResponse->getContent(), true)['data']['id'];
//
//        $this->json('GET', 'api/v1/user/projects', [], $this->httpHeader)
//            ->assertStatus(200);
//
//        $this->cleanupDatabaseOfTestProject($projectId, $profileId);
//
//        $this->cleanupDatabaseOfTestProfile($profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "PROJECT: canGetListOfProject: PASSED \n";
//    }
//
//    /** @test canNotCreateProjectWithoutName */
//    public function canNotCreateProjectWithoutName()
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
//        $projectModel         = factory(Project::class)->make()->toArray();
//
//        $projectModel['name'] = '';
//
//        $this->json('POST', 'api/v1/user/profiles/'.$profileId.'/projects', $projectModel, $this->httpHeader)
//            ->assertStatus(400);
//
//        $this->cleanupDatabaseOfTestProfile($profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "PROJECT: cannotCreateProjectWithoutName: PASSED \n";
//    }
//
//
//    /** @test canNotCreateProjectWithExistName */
//    public function canNotCreateProjectWithExistName()
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
//
//        $projectModel         = factory(Project::class)->make()->toArray();
//
//        $projectResponse = $this->json('POST', 'api/v1/user/profiles/'.$profileId.'/projects', $projectModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Project::$responseBody));
//
//        $projectId         = json_decode($projectResponse->getContent(), true)['data']['id'];
//        $projectName       = json_decode($projectResponse->getContent(), true)['data']['name'];
//
//        $projectModel['name'] = $projectName;
//        $this->json('POST', 'api/v1/user/profiles/'.$profileId.'/projects', $projectModel, $this->httpHeader)
//            ->assertStatus(400);
//
//        $this->cleanupDatabaseOfTestProject($projectId, $profileId);
//
//        $this->cleanupDatabaseOfTestProfile($profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "PROJECT: canNotCreateProjectWithExistName: PASSED \n";
//    }
//
//
//
//    /** @test canCreateNewProject */
//    public function canCreateNewProject()
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
//        $projectModel      = factory(Project::class)->make()->toArray();
//
//        $projectResponse   = $this->json('POST', 'api/v1/user/profiles/'.$profileId.'/projects', $projectModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Project::$responseBody));
//
//
//        $projectId         = json_decode($projectResponse->getContent(), true)['data']['id'];
//
//        $this->cleanupDatabaseOfTestProject($projectId, $profileId);
//
//        $this->cleanupDatabaseOfTestProfile($profileId);
//
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "PROJECT: canCreateNewProject: PASSED \n";
//    }
//
//    /** @test canGetSingleProject */
//    public function canGetSingleProject()
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
//        $projectModel             = factory(Project::class)->make()->toArray();
//
//        $projectResponse          = $this->json('POST', 'api/v1/user/profiles/'.$profileId.'/projects', $projectModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Project::$responseBody));
//
//        $projectId     = json_decode($projectResponse->getContent(), true)['data']['id'];
//
//        $this->json('GET', 'api/v1/user/profiles/'.$profileId.'/projects/'. $projectId, [], $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Project::$responseBody));
//
//        $this->cleanupDatabaseOfTestProject($projectId, $profileId);
//
//        $this->cleanupDatabaseOfTestProfile($profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "PROJECT: canGetSingleProject: PASSED \n";
//    }
//
//
//    /** @test canNotUpdateProjectWithoutName */
//    public function canNotUpdateProjectWithoutName()
//    {
//
//        $profileModel      = factory(Profile::class)->make()->toArray();
//
//        $profileResponse   = $this->json('POST', 'api/v1/user/profiles', $profileModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Profile::$responseBody))
//            ->assertJson(array("data"=>$profileModel));
//
//        $profileId         = json_decode($profileResponse->getContent(), true)['data']['id'];
//
//        $projectModel             = factory(Project::class)->make()->toArray();
//
//        $projectResponse          = $this->json('POST', 'api/v1/user/profiles/'.$profileId.'/projects', $projectModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Project::$responseBody));
//
//        $projectId     = json_decode($projectResponse->getContent(), true)['data']['id'];
//
//        $projectModel['name'] = '';
//
//        $this->json('POST', 'api/v1/user/profiles/'.$profileId.'/projects/'. $projectId.'/update', $projectModel, $this->httpHeader)
//            ->assertStatus(400);
//
//        $this->cleanupDatabaseOfTestProject($projectId, $profileId);
//
//        $this->cleanupDatabaseOfTestProfile($profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "PROJECT: canNotUpdateProjectWithoutName: PASSED \n";
//    }
//
//    /** @test canNotUpdateProjectWithExistName */
//    public function canNotUpdateProjectWithExistName()
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
//        $projectModel             = factory(Project::class)->make()->toArray();
//
//        $projectResponse          = $this->json('POST', 'api/v1/user/profiles/'.$profileId.'/projects', $projectModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Project::$responseBody));
//
//
//        $projectId     = json_decode($projectResponse->getContent(), true)['data']['id'];
//        $projectName     = json_decode($projectResponse->getContent(), true)['data']['name'];
//
//        $projectModel['name'] = $projectName;
//
//        $this->json('POST', 'api/v1/user/profiles/'.$profileId.'/projects/'. $projectId.'/update', $projectModel, $this->httpHeader)
//            ->assertStatus(200);
//
//        $this->cleanupDatabaseOfTestProject($projectId, $profileId);
//
//        $this->cleanupDatabaseOfTestProfile($profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "PROJECT: canNotUpdateProjectWithExistName: PASSED \n";
//    }
//
//    /** @test canUpdateProject */
//    public function canUpdateProject()
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
//        $projectAModel             = factory(Project::class)->make()->toArray();
//
//        $projectResponse          = $this->json('POST', 'api/v1/user/profiles/'.$profileId.'/projects', $projectAModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Project::$responseBody));
//
//        $projectId     = json_decode($projectResponse->getContent(), true)['data']['id'];
//
//        $projectBModel             = factory(Project::class)->make()->toArray();
//
//        $this->json('POST', 'api/v1/user/profiles/'.$profileId.'/projects/'. $projectId.'/update', $projectBModel, $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Project::$responseBody));
//
//        $this->cleanupDatabaseOfTestProject($projectId, $profileId);
//
//        $this->cleanupDatabaseOfTestProfile($profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "PROJECT: canUpdateProject: PASSED \n";
//    }
//
//    /** @test canDeleteProject */
//    public function canDeleteProject()
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
//        $projectModel             = factory(Project::class)->make()->toArray();
//
//        $projectResponse          = $this->json('POST', 'api/v1/user/profiles/'.$profileId.'/projects', $projectModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Project::$responseBody));
//
//        $projectId     = json_decode($projectResponse->getContent(), true)['data']['id'];
//
//
//        $this->json('DELETE', 'api/v1/user/profiles/'.$profileId.'/projects/'. $projectId, [], $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Project::$responseBody));
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "PROJECT: canDeleteProject: PASSED \n";
//    }
//
//    // Clean up function for project
//    public function cleanupDatabaseOfTestProject(int $projectId, int $profileId) {
//        $this->json('DELETE', 'api/v1/user/profiles/'.$profileId.'/projects/'. $projectId, [], $this->httpHeader)->assertStatus(200);
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
//
//
//}
