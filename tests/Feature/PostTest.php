<?php
//
//namespace Tests\Feature;
//
//use App\Exceptions\Handler;
//use App\Group;
//use App\Post;
//use App\User;
//use Illuminate\Contracts\Debug\ExceptionHandler;
//use Tests\TestCase;
//use Illuminate\Foundation\Testing\WithFaker;
//
//
//class PostTest extends TestCase
//{
//    use WithFaker;
//    protected $user;
//    protected $httpHeader;
//    protected $cleanUpAfterTests;
//
//    /*
//     * canGetListOfPost
//     * canNotCreatePostWithoutName
//     * canCreateNewPost
//     * canGetSinglePost
//     * canNotUpdatePostWithoutName
//     * canEditPost
//     * canDeletePost
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
//    /** @test canGetListOfPost*/
//    public function canGetListOfPost()
//    {
//        $postModel             = factory(Post::class)->make()->toArray();
//
//        $postResponse          = $this->json('POST', 'api/v1/user/posts', $postModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Post::$responseBody))
//            ->assertJson(array("data"=>$postModel));
//
//        $postId     = json_decode($postResponse->getContent(), true)['data']['id'];
//
//        $this->json('GET', 'api/v1/user/posts', [], $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>['0' => Post::$responseBody]))
//            ->assertJson(array("data"=>['0' => $postModel]));
//
//        $this->cleanupDatabaseOfTestPost($postId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "POST: canGetListOfPost: PASSED \n";
//    }
//
//    /** @test canNotCreatePostWithoutName */
//    public function canNotCreatePostWithoutName()
//    {
//        $postModel         = factory(Post::class)->make()->toArray();
//
//        $postModel['summary'] = '';
//
//        $this->json('POST', 'api/v1/user/posts', $postModel, $this->httpHeader)
//            ->assertStatus(400);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "POST: canNotCreatePostWithoutName: PASSED \n";
//    }
//
//
//    /** @test canCreateNewPost */
//    public function canCreateNewPost()
//    {
//        $postModel      = factory(Post::class)->make()->toArray();
//
//        $postResponse   = $this->json('POST', 'api/v1/user/posts', $postModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Post::$responseBody))
//            ->assertJson(array("data"=>$postModel));
//
//        $postId         = json_decode($postResponse->getContent(), true)['data']['id'];
//
//        $this->cleanupDatabaseOfTestPost($postId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "POST: canCreateNewPost: PASSED \n";
//    }
//
//    /** @test canGetSinglePost */
//    public function canGetSinglePost()
//    {
//        $postModel             = factory(Post::class)->make()->toArray();
//
//        $postResponse          = $this->json('POST', 'api/v1/user/posts', $postModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Post::$responseBody))
//            ->assertJson(array("data"=>$postModel));
//
//        $postId     = json_decode($postResponse->getContent(), true)['data']['id'];
//
//        $this->json('GET', 'api/v1/user/posts/'. $postId, [], $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Post::$responseBody))
//            ->assertJson(array("data"=>$postModel));
//
//        $this->cleanupDatabaseOfTestPost($postId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "POST: canGetSinglePost: PASSED \n";
//    }
//
//
//    /** @test canNotUpdatePostWithoutName */
//    public function canNotUpdatePostWithoutName()
//    {
//        $postModel             = factory(Post::class)->make()->toArray();
//
//        $postResponse          = $this->json('POST', 'api/v1/user/posts', $postModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Post::$responseBody))
//            ->assertJson(array("data"=>$postModel));
//
//        $postId     = json_decode($postResponse->getContent(), true)['data']['id'];
//
//        $postModel['summary'] = '';
//
//        $this->json('PUT', 'api/v1/user/posts/'. $postId, $postModel, $this->httpHeader)
//            ->assertStatus(400);
//
//        $this->cleanupDatabaseOfTestPost($postId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "POST: canNotUpdatePostWithoutName: PASSED \n";
//    }
//
//
//    /** @test canUpdatePost */
//    public function canUpdatePost()
//    {
//        $postAModel            = factory(Post::class)->make()->toArray();
//
//        $postResponse          = $this->json('POST', 'api/v1/user/posts', $postAModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Post::$responseBody))
//            ->assertJson(array("data"=>$postAModel));
//
//        $postId     = json_decode($postResponse->getContent(), true)['data']['id'];
//
//        $postBModel            = factory(Post::class)->make()->toArray();
//
//        $this->json('PUT', 'api/v1/user/posts/'. $postId, $postBModel, $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Post::$responseBody))
//            ->assertJson(array("data"=>$postBModel));
//
//        $this->cleanupDatabaseOfTestPost($postId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "POST: canUpdatePost: PASSED \n";
//    }
//
//    /** @test canDeletePost */
//    public function canDeletePost()
//    {
//        $postModel             = factory(Post::class)->make()->toArray();
//
//        $postResponse          = $this->json('POST', 'api/v1/user/posts', $postModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Post::$responseBody))
//            ->assertJson(array("data"=>$postModel));
//
//        $postId     = json_decode($postResponse->getContent(), true)['data']['id'];
//
//        $this->json('DELETE', 'api/v1/user/posts/'. $postId, [], $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Post::$responseBody))
//            ->assertJson(array("data"=>$postModel));
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "POST: canDeletePost: PASSED \n";
//    }
//
//    // Clean up function for Post
//    public function cleanupDatabaseOfTestPost(int $postId) {
//        $this->json('DELETE', 'api/v1/user/posts/'. $postId, [], $this->httpHeader)->assertStatus(200);
//    }
//
//    // Clean up function for user
//    public function cleanupDatabaseOfTestDataUser() {
//        $this->json('DELETE', 'api/v1/user',[], $this->httpHeader)->assertStatus(410);
//    }
//}
