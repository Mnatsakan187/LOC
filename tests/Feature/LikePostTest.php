<?php
//
//namespace Tests\Feature;
//
//use App\Exceptions\Handler;
//use App\Post;
//use App\Like;
//use App\User;
//use Illuminate\Contracts\Debug\ExceptionHandler;
//use Tests\TestCase;
//use Illuminate\Foundation\Testing\WithFaker;
//
//
//class LikePostTest extends TestCase
//{
//    use WithFaker;
//    protected $user;
//    protected $httpHeader;
//    protected $cleanUpAfterTests;
//
//    /*
//     * canGetListOfLikePost
//     * cannotCreateLikePost
//     * canCreateNewLikePost
//     * canGetSingleLikePost
//     * canDeleteLikePost
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
//    /** @test canGetListOfLikePost */
//    public function canGetListOfLikePost()
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
//        $likeModel      = factory(Like::class)->make()->toArray();
//
//        $likePostResponse = $this->json('POST', 'api/v1/user/posts/'.$postId.'/likes',  $likeModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Like::$responseBody))
//            ->assertJson(array("data"=>$likeModel));
//
//        $this->json('GET', 'api/v1/user/posts/'.$postId.'/likes', [], $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>['0' => Like::$responseBody]))
//            ->assertJson(array("data"=>['0' => $likeModel]));
//
//        $likeId     = json_decode($likePostResponse->getContent(), true)['data']['id'];
//
//        $this->cleanupDatabaseOfTestLikePost($postId, $likeId);
//
//        $this->cleanupDatabaseOfTestPost($postId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "LIKE-POST: canGetListOfLikePost: PASSED \n";
//    }
//
//    /** @test canNotCreateLikePostWithoutLikeData */
//    public function canNotCreateLikePostWithoutLikeData()
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
//        $likeModel      = factory(Like::class)->make()->toArray();
//
//        $likeModel['likedDate'] = '';
//
//        $this->json('POST', 'api/v1/user/posts/'.$postId.'/likes',  $likeModel,  $this->httpHeader)
//            ->assertStatus(400);
//
//        $this->cleanupDatabaseOfTestPost($postId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "LIKE-POST: canNotCreateLikePostWithoutLikeData: PASSED \n";
//    }
//
//    /** @test canCreateNewLikePost */
//    public function canCreateNewLikePost()
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
//        $likeModel      = factory(Like::class)->make()->toArray();
//
//        $likePostResponse = $this->json('POST', 'api/v1/user/posts/'.$postId.'/likes',  $likeModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Like::$responseBody))
//            ->assertJson(array("data"=>$likeModel));
//
//        $likeId         = json_decode($likePostResponse->getContent(), true)['data']['id'];
//
//        $this->cleanupDatabaseOfTestLikePost($postId, $likeId);
//
//        $this->cleanupDatabaseOfTestPost($postId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "LIKE-POST: canCreateNewLikePost: PASSED \n";
//    }
//
//    /** @test canGetSingleLikePost */
//    public function canGetSingleLikePost()
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
//        $likeModel      = factory(Like::class)->make()->toArray();
//
//        $likePostResponse = $this->json('POST', 'api/v1/user/posts/'.$postId.'/likes',  $likeModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Like::$responseBody))
//            ->assertJson(array("data"=>$likeModel));
//
//        $likeId         = json_decode($likePostResponse->getContent(), true)['data']['id'];
//
//        $this->json('GET', 'api/v1/user/posts/'.$postId.'/likes/'.$likeId, [], $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Like::$responseBody))
//            ->assertJson(array("data"=>$likeModel));
//
//        $this->cleanupDatabaseOfTestLikePost($postId, $likeId);
//
//        $this->cleanupDatabaseOfTestPost($postId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "LIKE-POST: canGetSingleLikePost: PASSED \n";
//    }
//
//    /** @test canDeleteLikePost */
//    public function canDeleteLikePost()
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
//        $likeModel      = factory(Like::class)->make()->toArray();
//
//        $likePostResponse = $this->json('POST', 'api/v1/user/posts/'.$postId.'/likes',  $likeModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Like::$responseBody))
//            ->assertJson(array("data"=>$likeModel));
//
//        $likeId         = json_decode($likePostResponse->getContent(), true)['data']['id'];
//
//        $this->json('DELETE', 'api/v1/user/posts/'.$postId.'/likes/'. $likeId, [], $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Like::$responseBody))
//            ->assertJson(array("data"=>$likeModel));
//
//        $this->cleanupDatabaseOfTestPost($postId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "LIKE-POST: canDeleteLikePost: PASSED \n";
//    }
//
//    // Clean up function for like post
//    public function cleanupDatabaseOfTestLikePost(int $postId, int $likeId) {
//        $this->json('DELETE', 'api/v1/user/posts/'.$postId.'/likes/'.$likeId, [], $this->httpHeader)->assertStatus(200);
//    }
//
//    // Clean up function for post
//    public function cleanupDatabaseOfTestPost(int $postId) {
//        $this->json('DELETE', 'api/v1/user/posts/'. $postId, [], $this->httpHeader)->assertStatus(200);
//    }
//
//    // Clean up function for user
//    public function cleanupDatabaseOfTestDataUser() {
//        $this->json('DELETE', 'api/v1/user',[], $this->httpHeader)->assertStatus(410);
//    }
//
//
//}
