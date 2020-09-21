<?php
//
//namespace Tests\Feature;
//
//use App\Comment;
//use App\Exceptions\Handler;
//use App\Profile;
//use App\Project;
//use App\User;
//use Illuminate\Contracts\Debug\ExceptionHandler;
//use Tests\TestCase;
//use Illuminate\Foundation\Testing\WithFaker;
//
//
//class CommentProjectTest extends TestCase
//{
//    use WithFaker;
//    protected $user;
//    protected $httpHeader;
//    protected $cleanUpAfterTests;
//
//    /*
//     * canGetListOfCommentProject
//     * canNotCreateCommentProjectWithoutName
//     * canNotCreateCommentProjectWithSameName
//     * canCreateNewCommentProject
//     * canGetSingleCommentProject
//     * canNotUpdateCommentProjectWithoutName
//     * canUpdateCommentProject
//     * canDeleteCommentProject
//     * cannotCreateCommentProject
//     * cannotUpdateCommentProject
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
//    /** @test canGetListOfCommentProject */
//    public function canGetListOfCommentProject()
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
//
//        $commentModel      = factory(Comment::class)->make()->toArray();
//
//        $commentProjectResponse = $this->json('POST', 'api/v1/user/projects/'.$projectId.'/comments',  $commentModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Comment::$responseBody))
//            ->assertJson(array("data"=>$commentModel));
//
//        $this->json('GET', 'api/v1/user/projects/'.$projectId.'/comments', [], $this->httpHeader)
//            ->assertStatus(200);
//
//        $commentId     = json_decode($commentProjectResponse->getContent(), true)['data']['id'];
//
//        $this->cleanupDatabaseOfTestCommentProject($projectId, $commentId);
//
//        $this->cleanupDatabaseOfTestProject($projectId, $profileId);
//
//        $this->cleanupDatabaseOfTestProfile($profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "COMMENT-PROJECT: canGetListOfCommentProject: PASSED \n";
//    }
//
//    /** @test canNotCreateCommentProjectWithoutName */
//    public function canNotCreateCommentProjectWithoutName()
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
//        $commentModel      = factory(Comment::class)->make()->toArray();
//
//        $commentModel['commentText'] = '';
//
//        $this->json('POST', 'api/v1/user/projects/'.$projectId.'/comments',  $commentModel,  $this->httpHeader)
//            ->assertStatus(400);
//
//        $this->cleanupDatabaseOfTestProject($projectId, $profileId);
//
//        $this->cleanupDatabaseOfTestProfile($profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "COMMENT-PROJECT: canNotCreateCommentProjectWithoutName: PASSED \n";
//    }
//
//
//
//
//    /** @test canCreateNewCommentProject */
//    public function canCreateNewCommentProject()
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
//        $commentModel      = factory(Comment::class)->make()->toArray();
//
//        $commentProjectResponse = $this->json('POST', 'api/v1/user/projects/'.$projectId.'/comments',  $commentModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Comment::$responseBody))
//            ->assertJson(array("data"=>$commentModel));
//
//        $commentId         = json_decode($commentProjectResponse->getContent(), true)['data']['id'];
//
//        $this->cleanupDatabaseOfTestCommentProject($projectId, $commentId);
//
//        $this->cleanupDatabaseOfTestProject($projectId, $profileId);
//
//        $this->cleanupDatabaseOfTestProfile($profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "COMMENT-PROJECT: canCreateNewCommentProject: PASSED \n";
//    }
//
//    /** @test canGetSingleCommentProject */
//    public function canGetSingleCommentProject()
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
//        $commentModel      = factory(Comment::class)->make()->toArray();
//
//        $commentProjectResponse = $this->json('POST', 'api/v1/user/projects/'.$projectId.'/comments',  $commentModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Comment::$responseBody))
//            ->assertJson(array("data"=>$commentModel));
//
//        $commentId         = json_decode($commentProjectResponse->getContent(), true)['data']['id'];
//
//        $this->json('GET', 'api/v1/user/projects/'.$projectId.'/comments/'.$commentId, [], $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Comment::$responseBody))
//            ->assertJson(array("data"=>$commentModel));
//
//        $this->cleanupDatabaseOfTestCommentProject($projectId, $commentId);
//
//        $this->cleanupDatabaseOfTestProject($projectId, $profileId);
//
//        $this->cleanupDatabaseOfTestProfile($profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "COMMENT-PROJECT: canGetSingleCommentProject: PASSED \n";
//    }
//
//    /** @test canNotUpdateCommentProjectWithoutName */
//    public function canNotUpdateCommentProjectWithoutName()
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
//        $commentModel      = factory(Comment::class)->make()->toArray();
//
//        $commentProjectResponse = $this->json('POST', 'api/v1/user/projects/'.$projectId.'/comments',  $commentModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Comment::$responseBody))
//            ->assertJson(array("data"=>$commentModel));
//
//        $commentId         = json_decode($commentProjectResponse->getContent(), true)['data']['id'];
//
//        $commentModel      = factory(Comment::class)->make()->toArray();
//
//        $commentModel['commentText'] = '';
//
//        $this->json('PUT', 'api/v1/user/projects/'.$projectId.'/comments/'. $commentId, $commentModel, $this->httpHeader)
//            ->assertStatus(400);
//
//        $this->cleanupDatabaseOfTestCommentProject($projectId, $commentId);
//
//        $this->cleanupDatabaseOfTestProject($projectId, $profileId);
//
//        $this->cleanupDatabaseOfTestProfile($profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "COMMENT-PROJECT: canNotUpdateCommentProjectWithoutName: PASSED \n";
//    }
//
//
//    /** @test canUpdateCommentProject */
//    public function canUpdateCommentProject()
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
//        $commentAModel      = factory(Comment::class)->make()->toArray();
//
//        $commentProjectResponse = $this->json('POST', 'api/v1/user/projects/'.$projectId.'/comments',  $commentAModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Comment::$responseBody))
//            ->assertJson(array("data"=>$commentAModel));
//
//        $commentId         = json_decode($commentProjectResponse->getContent(), true)['data']['id'];
//
//        $commentBModel      = factory(Comment::class)->make()->toArray();
//
//        $this->json('PUT', 'api/v1/user/projects/'.$projectId.'/comments/'. $commentId, $commentBModel, $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Comment::$responseBody))
//            ->assertJson(array("data"=>$commentBModel));
//
//        $this->cleanupDatabaseOfTestCommentProject($projectId, $commentId);
//
//        $this->cleanupDatabaseOfTestProject($projectId, $profileId);
//
//        $this->cleanupDatabaseOfTestProfile($profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "COMMENT-PROJECT: canUpdateCommentProject: PASSED \n";
//    }
//
//    /** @test canDeleteCommentProject */
//    public function canDeleteCommentProject()
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
//        $commentModel      = factory(Comment::class)->make()->toArray();
//
//        $commentProjectResponse = $this->json('POST', 'api/v1/user/projects/'.$projectId.'/comments',  $commentModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Comment::$responseBody))
//            ->assertJson(array("data"=>$commentModel));
//
//        $commentId         = json_decode($commentProjectResponse->getContent(), true)['data']['id'];
//
//        $this->json('DELETE', 'api/v1/user/projects/'.$projectId.'/comments/'. $commentId, [], $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Comment::$responseBody))
//            ->assertJson(array("data"=>$commentModel));
//
//        $this->cleanupDatabaseOfTestProject($projectId, $profileId);
//
//        $this->cleanupDatabaseOfTestProfile($profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "COMMENT-PROJECT: canDeleteCommentProject: PASSED \n";
//    }
//
//    // Clean up function for comment project
//    public function cleanupDatabaseOfTestCommentProject(int $projectId, int $commentId) {
//        $this->json('DELETE', 'api/v1/user/projects/'.$projectId.'/comments/'.$commentId, [], $this->httpHeader)->assertStatus(200);
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
