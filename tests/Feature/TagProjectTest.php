<?php
//
//namespace Tests\Feature;
//
//use App\Exceptions\Handler;
//use App\Profile;
//use App\Project;
//use App\Tag;
//use App\User;
//use Illuminate\Contracts\Debug\ExceptionHandler;
//use Tests\TestCase;
//use Illuminate\Foundation\Testing\WithFaker;
//
//
//class TagProjectTest extends TestCase
//{
//    use WithFaker;
//    protected $user;
//    protected $httpHeader;
//    protected $cleanUpAfterTests;
//
//    /*
//     * canGetListOfTagProject
//     * canNotCreateTagProjectWithoutName
//     * canNotCreateTagProjectWithSameName
//     * canCreateNewTagProject
//     * canGetSingleTagProject
//     * canNotUpdateTagProjectWithoutName
//     * canUpdateTagProject
//     * canDeleteTagProject
//     * cannotCreateTagProject
//     * cannotUpdateTagProject
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
//            'password'  => 123456,
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
//    /** @test canGetListOfTagProject */
//    public function canGetListOfTagProject()
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
//
//        $projectResponse          = $this->json('POST', 'api/v1/user/profiles/'.$profileId.'/projects', $projectModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Project::$responseBody));
//
//
//        $projectId     = json_decode($projectResponse->getContent(), true)['data']['id'];
//
//        $tagModel      = factory(Tag::class)->make()->toArray();
//
//        $tagProjectResponse = $this->json('POST', 'api/v1/user/projects/'.$projectId.'/tags',  $tagModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Tag::$responseBody))
//            ->assertJson(array("data"=>$tagModel));
//
//        $this->json('GET', 'api/v1/user/projects/'.$projectId.'/tags', [], $this->httpHeader)
//            ->assertStatus(200);
//
//        $tagId     = json_decode($tagProjectResponse->getContent(), true)['data']['id'];
//
//        $this->cleanupDatabaseOfTestTagProject($projectId, $tagId);
//
//        $this->cleanupDatabaseOfTestProject($projectId, $profileId);
//
//        $this->cleanupDatabaseOfTestProfile($profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "TAG-PROJECT: canGetListOfTagProject: PASSED \n";
//    }
//
//    /** @test canNotCreateTagProjectWithoutName */
//    public function canNotCreateTagProjectWithoutName()
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
//        $tagModel      = factory(Tag::class)->make()->toArray();
//
//        $tagModel['name'] = '';
//
//        $this->json('POST', 'api/v1/user/projects/'.$projectId.'/tags',  $tagModel,  $this->httpHeader)
//            ->assertStatus(400);
//
//        $this->cleanupDatabaseOfTestProject($projectId, $profileId);
//
//        $this->cleanupDatabaseOfTestProfile($profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "TAG-PROJECT: canNotCreateTagProjectWithoutName: PASSED \n";
//    }
//
//
//    /** @test canNotCreateTagProjectWithExistName */
//    public function canNotCreateTagProjectWithExistName()
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
//        $projectResponse = $this->json('POST', 'api/v1/user/profiles/'.$profileId.'/projects', $projectModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Project::$responseBody));
//
//        $projectId         = json_decode($projectResponse->getContent(), true)['data']['id'];
//
//        $tagModel      = factory(Tag::class)->make()->toArray();
//
//        $tagProjectResponse = $this->json('POST', 'api/v1/user/projects/'.$projectId.'/tags',  $tagModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Tag::$responseBody))
//            ->assertJson(array("data"=>$tagModel));
//
//        $tagName         = json_decode($tagProjectResponse->getContent(), true)['data']['name'];
//        $tagId         = json_decode($tagProjectResponse->getContent(), true)['data']['id'];
//
//        $tagModel['name'] = $tagName;
//
//        $this->json('POST', 'api/v1/user/projects/'.$projectId.'/tags',  $tagModel,  $this->httpHeader)
//            ->assertStatus(400);
//
//        $this->cleanupDatabaseOfTestTagProject($projectId, $tagId);
//
//        $this->cleanupDatabaseOfTestProject($projectId, $profileId);
//
//        $this->cleanupDatabaseOfTestProfile($profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "TAG-PROJECT: canNotCreateTagProjectWithExistName: PASSED \n";
//    }
//
//    /** @test canCreateNewTagProject */
//    public function canCreateNewTagProject()
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
//        $projectId         = json_decode($projectResponse->getContent(), true)['data']['id'];
//
//        $tagModel      = factory(Tag::class)->make()->toArray();
//
//        $tagProjectResponse = $this->json('POST', 'api/v1/user/projects/'.$projectId.'/tags',  $tagModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Tag::$responseBody))
//            ->assertJson(array("data"=>$tagModel));
//
//        $tagId         = json_decode($tagProjectResponse->getContent(), true)['data']['id'];
//
//        $this->cleanupDatabaseOfTestTagProject($projectId, $tagId);
//
//        $this->cleanupDatabaseOfTestProject($projectId, $profileId);
//
//        $this->cleanupDatabaseOfTestProfile($profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "TAG-PROJECT: canCreateNewTagProject: PASSED \n";
//    }
//
//    /** @test canGetSingleTagProject */
//    public function canGetSingleTagProject()
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
//        $tagModel      = factory(Tag::class)->make()->toArray();
//
//        $tagProjectResponse = $this->json('POST', 'api/v1/user/projects/'.$projectId.'/tags',  $tagModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Tag::$responseBody))
//            ->assertJson(array("data"=>$tagModel));
//
//        $tagId         = json_decode($tagProjectResponse->getContent(), true)['data']['id'];
//
//        $this->json('GET', 'api/v1/user/projects/'.$projectId.'/tags/'.$tagId, [], $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Tag::$responseBody))
//            ->assertJson(array("data"=>$tagModel));
//
//        $this->cleanupDatabaseOfTestTagProject($projectId, $tagId);
//
//        $this->cleanupDatabaseOfTestProject($projectId, $profileId);
//
//        $this->cleanupDatabaseOfTestProfile($profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "TAG-PROJECT: canGetSingleTagProject: PASSED \n";
//    }
//
//    /** @test canNotUpdateTagProjectWithoutName */
//    public function canNotUpdateTagProjectWithoutName()
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
//
//        $tagModel      = factory(Tag::class)->make()->toArray();
//
//        $tagProjectResponse = $this->json('POST', 'api/v1/user/projects/'.$projectId.'/tags',  $tagModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Tag::$responseBody))
//            ->assertJson(array("data"=>$tagModel));
//
//        $tagId         = json_decode($tagProjectResponse->getContent(), true)['data']['id'];
//
//        $tagModel      = factory(Tag::class)->make()->toArray();
//
//        $tagModel['name'] = '';
//
//        $this->json('PUT', 'api/v1/user/projects/'.$projectId.'/tags/'. $tagId, $tagModel, $this->httpHeader)
//            ->assertStatus(400);
//
//        $this->cleanupDatabaseOfTestTagProject($projectId, $tagId);
//
//        $this->cleanupDatabaseOfTestProject($projectId, $profileId);
//
//        $this->cleanupDatabaseOfTestProfile($profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "TAG-PROJECT: canNotUpdateTagProjectWithoutName: PASSED \n";
//    }
//
//    /** @test canNotUpdateTagProjectWithExistName */
//    public function canNotUpdateTagProjectWithExistName()
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
//        $tagModel      = factory(Tag::class)->make()->toArray();
//
//        $tagProjectResponse = $this->json('POST', 'api/v1/user/projects/'.$projectId.'/tags',  $tagModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Tag::$responseBody))
//            ->assertJson(array("data"=>$tagModel));
//
//        $tagId         = json_decode($tagProjectResponse->getContent(), true)['data']['id'];
//
//        $tagName         = json_decode($tagProjectResponse->getContent(), true)['data']['name'];
//
//        $tagModel      = factory(Tag::class)->make()->toArray();
//
//        $tagModel['name'] = $tagName;
//
//        $this->json('PUT', 'api/v1/user/projects/'.$projectId.'/tags/'. $tagId, $tagModel, $this->httpHeader)
//            ->assertStatus(400);
//
//        $this->cleanupDatabaseOfTestTagProject($projectId, $tagId);
//
//        $this->cleanupDatabaseOfTestProject($projectId, $profileId);
//
//        $this->cleanupDatabaseOfTestProfile($profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "TAG-PROJECT: canNotUpdateTagProjectWithExistName: PASSED \n";
//    }
//
//    /** @test canUpdateTagProject */
//    public function canUpdateTagProject()
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
//        $tagAModel      = factory(Tag::class)->make()->toArray();
//
//        $tagProjectResponse = $this->json('POST', 'api/v1/user/projects/'.$projectId.'/tags',  $tagAModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Tag::$responseBody))
//            ->assertJson(array("data"=>$tagAModel));
//
//        $tagId         = json_decode($tagProjectResponse->getContent(), true)['data']['id'];
//
//        $tagBModel      = factory(Tag::class)->make()->toArray();
//
//        $this->json('PUT', 'api/v1/user/projects/'.$projectId.'/tags/'. $tagId, $tagBModel, $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Tag::$responseBody))
//            ->assertJson(array("data"=>$tagBModel));
//
//        $this->cleanupDatabaseOfTestTagProject($projectId, $tagId);
//
//        $this->cleanupDatabaseOfTestProject($projectId, $profileId);
//
//        $this->cleanupDatabaseOfTestProfile($profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "TAG-PROJECT: canUpdateTagProject: PASSED \n";
//    }
//
//    /** @test canDeleteTagProject */
//    public function canDeleteTagProject()
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
//        $projectId         = json_decode($projectResponse->getContent(), true)['data']['id'];
//
//        $tagModel      = factory(Tag::class)->make()->toArray();
//
//        $tagProjectResponse = $this->json('POST', 'api/v1/user/projects/'.$projectId.'/tags',  $tagModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Tag::$responseBody))
//            ->assertJson(array("data"=>$tagModel));
//
//        $tagId         = json_decode($tagProjectResponse->getContent(), true)['data']['id'];
//
//        $this->json('DELETE', 'api/v1/user/projects/'.$projectId.'/tags/'. $tagId, [], $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Tag::$responseBody))
//            ->assertJson(array("data"=>$tagModel));
//
//        $this->cleanupDatabaseOfTestProject($projectId, $profileId);
//
//        $this->cleanupDatabaseOfTestProfile($profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "TAG-PROJECT: canDeleteTagProject: PASSED \n";
//    }
//
//    // Clean up function for tag project
//    public function cleanupDatabaseOfTestTagProject(int $projectId, int $tagId) {
//        $this->json('DELETE', 'api/v1/user/projects/'.$projectId.'/tags/'.$tagId, [], $this->httpHeader)->assertStatus(200);
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
//    // Clean up function for profile
//    public function cleanupDatabaseOfTestProfile(int $profileId) {
//        $this->json('DELETE', 'api/v1/user/profiles/'. $profileId, [], $this->httpHeader)->assertStatus(200);
//    }
//
//
//}
