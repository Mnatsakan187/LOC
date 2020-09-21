<?php
//
//namespace Tests\Feature;
//
//use App\Exceptions\Handler;
//use App\Profile;
//use App\Project;
//use App\team;
//use App\User;
//use Illuminate\Contracts\Debug\ExceptionHandler;
//use Tests\TestCase;
//use Illuminate\Foundation\Testing\WithFaker;
//
//
//class TeamProjectTest extends TestCase
//{
//    use WithFaker;
//    protected $user;
//    protected $httpHeader;
//    protected $cleanUpAfterTests;
//
//    /*
//     * canGetListOfTeamProject
//     * cannotCreateTeamProject
//     * canCreateNewTeamProject
//     * canGetSingleTeamProject
//     * canDeleteTeamProject
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
//    /** @test canGetListOfTeamProject */
//    public function canGetListOfTeamProject()
//    {
//        $projectModel             = factory(Project::class)->make()->toArray();
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
//        $projectResponse          = $this->json('POST', 'api/v1/user/profiles/'.$profileId.'/projects', $projectModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Project::$responseBody));
//
//        $projectId     = json_decode($projectResponse->getContent(), true)['data']['id'];
//
//        $teamProjectResponse = $this->json('POST', 'api/v1/user/projects/'.$projectId.'/teams',  ['userId'=> $this->user->id],  $this->httpHeader)
//            ->assertStatus(201);
//
//        $this->json('GET', 'api/v1/user/projects/'.$projectId.'/teams', [], $this->httpHeader)
//            ->assertStatus(200);
//
//        $teamId     = json_decode($teamProjectResponse->getContent(), true)['data']['id'];
//
//        $this->cleanupDatabaseOfTestProjectTeam($projectId, $teamId);
//
//        $this->cleanupDatabaseOfTestProject($projectId, $profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "TEAM-PROJECT: canGetListOfTeamProject: PASSED \n";
//    }
//
//    /** @test cannotCreateTeamProjectWithoutUserId */
//    public function cannotCreateTeamProjectWithoutUserId()
//    {
//        $projectModel             = factory(Project::class)->make()->toArray();
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
//        $projectResponse          = $this->json('POST', 'api/v1/user/profiles/'.$profileId.'/projects', $projectModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Project::$responseBody));
//
//        $projectId     = json_decode($projectResponse->getContent(), true)['data']['id'];
//
//        $this->json('POST', 'api/v1/user/projects/'.$projectId.'/teams',  ['userId'=> ''],  $this->httpHeader)
//            ->assertStatus(400);
//
//        $this->cleanupDatabaseOfTestProject($projectId, $profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "TEAM-PROJECT: cannotCreateTeamProjectWithoutUserId: PASSED \n";
//    }
//
//    /** @test canCreateNewTeamProject */
//    public function canCreateNewTeamProject()
//    {
//        $projectModel             = factory(Project::class)->make()->toArray();
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
//        $projectResponse          = $this->json('POST', 'api/v1/user/profiles/'.$profileId.'/projects', $projectModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Project::$responseBody));
//
//        $projectId     = json_decode($projectResponse->getContent(), true)['data']['id'];
//
//        $teamProjectResponse = $this->json('POST', 'api/v1/user/projects/'.$projectId.'/teams',  ['userId'=> $this->user->id],  $this->httpHeader)
//            ->assertStatus(201);
//
//        $teamId     = json_decode($teamProjectResponse->getContent(), true)['data']['id'];
//
//        $this->cleanupDatabaseOfTestProjectTeam($projectId, $teamId);
//
//        $this->cleanupDatabaseOfTestProject($projectId, $profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "TEAM-PROJECT: canCreateNewTeamProject: PASSED \n";
//    }
//
//    /** @test canGetSingleTeamProject */
//    public function canGetSingleTeamProject()
//    {
//
//        $projectModel             = factory(Project::class)->make()->toArray();
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
//        $projectResponse          = $this->json('POST', 'api/v1/user/profiles/'.$profileId.'/projects', $projectModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Project::$responseBody));
//
//        $projectId     = json_decode($projectResponse->getContent(), true)['data']['id'];
//
//        $teamProjectResponse = $this->json('POST', 'api/v1/user/projects/'.$projectId.'/teams',  ['userId'=> $this->user->id],  $this->httpHeader)
//            ->assertStatus(201);
//
//        $teamId     = json_decode($teamProjectResponse->getContent(), true)['data']['id'];
//
//        $this->json('GET', 'user/projects/'.$projectId.'/teams/'.$teamId, [], $this->httpHeader)
//            ->assertStatus(200);
//
//        $this->cleanupDatabaseOfTestProjectTeam($projectId, $teamId);
//
//        $this->cleanupDatabaseOfTestProject($projectId, $profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "TEAM-PROJECT: canGetSingleTeamProject: PASSED \n";
//    }
//
//    /** @test canDeleteTeamProject */
//    public function canDeleteTeamProject()
//    {
//        $projectModel             = factory(Project::class)->make()->toArray();
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
//        $projectResponse          = $this->json('POST', 'api/v1/user/profiles/'.$profileId.'/projects', $projectModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Project::$responseBody));
//
//        $projectId     = json_decode($projectResponse->getContent(), true)['data']['id'];
//
//        $teamProjectResponse = $this->json('POST', 'api/v1/user/projects/'.$projectId.'/teams',  ['userId'=> $this->user->id],  $this->httpHeader)
//            ->assertStatus(201);
//
//        $teamId     = json_decode($teamProjectResponse->getContent(), true)['data']['id'];
//
//        $this->json('DELETE', 'api/v1/user/projects/'.$projectId.'/teams/'.$teamId, [], $this->httpHeader)->assertStatus(200);
//
//        $this->cleanupDatabaseOfTestProject($projectId, $profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "TEAM-PROJECT: canDeleteTeamProject: PASSED \n";
//    }
//
//    // Clean up function for team project
//    public function cleanupDatabaseOfTestProjectTeam(int $projectId, int $teamId) {
//        $this->json('DELETE', 'api/v1/user/projects/'.$projectId.'/teams/'.$teamId, [], $this->httpHeader)->assertStatus(200);
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
//
//}
