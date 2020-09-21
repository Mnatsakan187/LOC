<?php
//
//namespace Tests\Feature;
//
//use App\Exceptions\Handler;
//use App\Project;
//use App\Media;
//use App\User;
//use Illuminate\Contracts\Debug\ExceptionHandler;
//use Tests\TestCase;
//use Illuminate\Foundation\Testing\WithFaker;
//
//
//class MediaProjectTest extends TestCase
//{
//    use WithFaker;
//    protected $user;
//    protected $httpHeader;
//    protected $cleanUpAfterTests;
//
//    /*
//     * canGetListOfMediaProject
//     * canNotCreateMediaProjectWithoutName
//     * canNotCreateMediaProjectWithExistName
//     * canCreateMediaProject
//     * canGetSingleMediaProject
//     * canNotUpdateMediaProjectWithoutName
//     * canNotUpdateMediaProjectWithExistName
//     * canUpdateMediaProject
//     * canDeleteMediaProject
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
//    /** @test canGetListOfMediaProject */
//    public function canGetListOfMediaProject()
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
//        $mediaModel      = factory(Media::class)->make()->toArray();
//
//        $mediaProjectResponse = $this->json('POST', 'api/v1/user/projects/'.$projectId.'/media',  $mediaModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Media::$responseBody))
//            ->assertJson(array("data"=>$mediaModel));
//
//        $this->json('GET', 'api/v1/user/projects/'.$projectId.'/media', [], $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data" => ['0' => Media::$responseBody]))
//            ->assertJson(array("data"=>['0' => $mediaModel]));
//
//        $mediaId     = json_decode($mediaProjectResponse->getContent(), true)['data']['id'];
//
//        $this->cleanupDatabaseOfTestMediaProject($projectId, $mediaId);
//
//        $this->cleanupDatabaseOfTestProject($projectId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "MEDIA-PROJECT: canGetListOfMediaProject: PASSED \n";
//    }
//
//    /** @test canNotCreateMediaProjectWithoutName */
//    public function canNotCreateMediaProjectWithoutName()
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
//        $mediaModel      = factory(Media::class)->make()->toArray();
//
//        $mediaModel['displayName'] = '';
//
//        $this->json('POST', 'api/v1/user/projects/'.$projectId.'/media',  $mediaModel,  $this->httpHeader)
//            ->assertStatus(400);
//
//        $this->cleanupDatabaseOfTestProject($projectId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "MEDIA-PROJECT: canNotCreateMediaProjectWithoutName: PASSED \n";
//    }
//
//
//
//    /** @test canCreateMediaProject */
//    public function canCreateMediaProject()
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
//        $mediaModel      = factory(Media::class)->make()->toArray();
//
//        $mediaProjectResponse = $this->json('POST', 'api/v1/user/projects/'.$projectId.'/media',  $mediaModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Media::$responseBody));
//
//        $mediaId         = json_decode($mediaProjectResponse->getContent(), true)['data']['id'];
//
//        $this->cleanupDatabaseOfTestMediaProject($projectId, $mediaId);
//
//        $this->cleanupDatabaseOfTestProject($projectId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "MEDIA-PROJECT: canCreateMediaProject: PASSED \n";
//    }
//
//    /** @test canGetSingleMediaProject */
//    public function canGetSingleMediaProject()
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
//        $mediaModel      = factory(Media::class)->make()->toArray();
//
//        $mediaProjectResponse = $this->json('POST', 'api/v1/user/projects/'.$projectId.'/media',  $mediaModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Media::$responseBody));
//
//        $mediaId         = json_decode($mediaProjectResponse->getContent(), true)['data']['id'];
//
//        $this->json('GET', 'api/v1/user/projects/'.$projectId.'/media/'.$mediaId, [], $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Media::$responseBody))
//            ->assertJson(array("data"=>$mediaModel));
//
//        $this->cleanupDatabaseOfTestMediaProject($projectId, $mediaId);
//
//        $this->cleanupDatabaseOfTestProject($projectId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "MEDIA-PROJECT: canGetSingleMediaProject: PASSED \n";
//    }
//
//
//
//    /** @test canNotUpdateMediaProjectWithExistName */
//
//
//    /** @test canUpdateMediaProject */
//    public function canUpdateMediaProject()
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
//        $mediaAModel      = factory(Media::class)->make()->toArray();
//
//        $mediaProjectResponse = $this->json('POST', 'api/v1/user/projects/'.$projectId.'/media',  $mediaAModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Media::$responseBody));
//
//        $mediaId         = json_decode($mediaProjectResponse->getContent(), true)['data']['id'];
//
//        $mediaBModel      = factory(Media::class)->make()->toArray();
//
//        $this->json('PUT', 'api/v1/user/projects/'.$projectId.'/media/'. $mediaId, $mediaBModel, $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Media::$responseBody));
//
//        $this->cleanupDatabaseOfTestMediaProject($projectId, $mediaId);
//
//        $this->cleanupDatabaseOfTestProject($projectId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "MEDIA-PROJECT: canUpdateMediaProject: PASSED \n";
//    }
//
//    /** @test canDeleteMediaProject */
//    public function canDeleteMediaProject()
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
//        $mediaModel      = factory(Media::class)->make()->toArray();
//
//        $mediaProjectResponse = $this->json('POST', 'api/v1/user/projects/'.$projectId.'/media',  $mediaModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Media::$responseBody))
//            ->assertJson(array("data"=>$mediaModel));
//
//        $mediaId         = json_decode($mediaProjectResponse->getContent(), true)['data']['id'];
//
//        $this->json('DELETE', 'api/v1/user/projects/'.$projectId.'/media/'. $mediaId, [], $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Media::$responseBody))
//            ->assertJson(array("data"=>$mediaModel));
//
//        $this->cleanupDatabaseOfTestProject($projectId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "MEDIA-PROJECT: canDeleteMediaProject: PASSED \n";
//    }
//
//    // Clean up function for media project
//    public function cleanupDatabaseOfTestMediaProject(int $projectId, int $mediaId) {
//        $this->json('DELETE', 'api/v1/user/projects/'.$projectId.'/media/'.$mediaId, [], $this->httpHeader)->assertStatus(200);
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
