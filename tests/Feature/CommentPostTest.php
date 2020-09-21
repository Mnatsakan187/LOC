<?php
//
//namespace Tests\Feature;
//
//use App\Comment;
//use App\Exceptions\Handler;
//use App\Post;
//use App\Profile;
//use App\User;
//use Illuminate\Contracts\Debug\ExceptionHandler;
//use Tests\TestCase;
//use Illuminate\Foundation\Testing\WithFaker;
//
//
//class CommentPostTest extends TestCase
//{
//    use WithFaker;
//    protected $user;
//    protected $httpHeader;
//    protected $cleanUpAfterTests;
//
//    /*
//     * canGetListOfCommentPost
//     * canNotCreateCommentPostWithoutName
//     * canNotCreateCommentPostWithSameName
//     * canCreateNewCommentPost
//     * canGetSingleCommentPost
//     * canNotUpdateCommentPostWithoutName
//     * canUpdateCommentPost
//     * canDeleteCommentPost
//     * cannotCreateCommentPost
//     * cannotUpdateCommentPost
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
//    /** @test canGetListOfCommentPost */
//    public function canGetListOfCommentPost()
//    {
//        $postModel             = factory(Post::class)->make()->toArray();
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
//        $postResponse          = $this->json('POST', 'api/v1/user/posts', $postModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Post::$responseBody));
//
//        $postId     = json_decode($postResponse->getContent(), true)['data']['id'];
//
//
//        $commentModel      = factory(Comment::class)->make()->toArray();
//
//        $commentPostResponse = $this->json('POST', 'api/v1/user/posts/'.$postId.'/comments',  $commentModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Comment::$responseBody))
//            ->assertJson(array("data"=>$commentModel));
//
//        $this->json('GET', 'api/v1/user/posts/'.$postId.'/comments', [], $this->httpHeader)
//            ->assertStatus(200);
//
//        $commentId     = json_decode($commentPostResponse->getContent(), true)['data']['id'];
//
//        $this->cleanupDatabaseOfTestCommentPost($postId, $commentId);
//
//        $this->cleanupDatabaseOfTestPost($postId, $profileId);
//
//        $this->cleanupDatabaseOfTestProfile($profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "COMMENT-POST: canGetListOfCommentPost: PASSED \n";
//    }
//
//    /** @test canNotCreateCommentPostWithoutName */
//    public function canNotCreateCommentPostWithoutName()
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
//        $postModel             = factory(Post::class)->make()->toArray();
//
//        $postResponse          = $this->json('POST', 'api/v1/user/posts', $postModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Post::$responseBody));
//
//        $postId     = json_decode($postResponse->getContent(), true)['data']['id'];
//
//        $commentModel      = factory(Comment::class)->make()->toArray();
//
//        $commentModel['commentText'] = '';
//
//        $this->json('POST', 'api/v1/user/posts/'.$postId.'/comments',  $commentModel,  $this->httpHeader)
//            ->assertStatus(400);
//
//        $this->cleanupDatabaseOfTestPost($postId, $profileId);
//
//        $this->cleanupDatabaseOfTestProfile($profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "COMMENT-POST: canNotCreateCommentPostWithoutName: PASSED \n";
//    }
//
//
//
//
//    /** @test canCreateNewCommentPost */
//    public function canCreateNewCommentPost()
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
//        $postModel      = factory(Post::class)->make()->toArray();
//
//        $postResponse   = $this->json('POST', 'api/v1/user/posts', $postModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Post::$responseBody));
//
//        $postId         = json_decode($postResponse->getContent(), true)['data']['id'];
//
//        $commentModel      = factory(Comment::class)->make()->toArray();
//
//        $commentPostResponse = $this->json('POST', 'api/v1/user/posts/'.$postId.'/comments',  $commentModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Comment::$responseBody))
//            ->assertJson(array("data"=>$commentModel));
//
//        $commentId         = json_decode($commentPostResponse->getContent(), true)['data']['id'];
//
//        $this->cleanupDatabaseOfTestCommentPost($postId, $commentId);
//
//        $this->cleanupDatabaseOfTestPost($postId, $profileId);
//
//        $this->cleanupDatabaseOfTestProfile($profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "COMMENT-POST: canCreateNewCommentPost: PASSED \n";
//    }
//
//    /** @test canGetSingleCommentPost */
//    public function canGetSingleCommentPost()
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
//        $postModel             = factory(Post::class)->make()->toArray();
//
//        $postResponse          = $this->json('POST', 'api/v1/user/posts', $postModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Post::$responseBody));
//
//        $postId     = json_decode($postResponse->getContent(), true)['data']['id'];
//
//
//        $commentModel      = factory(Comment::class)->make()->toArray();
//
//        $commentPostResponse = $this->json('POST', 'api/v1/user/posts/'.$postId.'/comments',  $commentModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Comment::$responseBody))
//            ->assertJson(array("data"=>$commentModel));
//
//        $commentId         = json_decode($commentPostResponse->getContent(), true)['data']['id'];
//
//        $this->json('GET', 'api/v1/user/posts/'.$postId.'/comments/'.$commentId, [], $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Comment::$responseBody))
//            ->assertJson(array("data"=>$commentModel));
//
//        $this->cleanupDatabaseOfTestCommentPost($postId, $commentId);
//
//        $this->cleanupDatabaseOfTestPost($postId, $profileId);
//
//        $this->cleanupDatabaseOfTestProfile($profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "COMMENT-POST: canGetSingleCommentPost: PASSED \n";
//    }
//
//    /** @test canNotUpdateCommentPostWithoutName */
//    public function canNotUpdateCommentPostWithoutName()
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
//        $postModel             = factory(Post::class)->make()->toArray();
//
//        $postResponse          = $this->json('POST', 'api/v1/user/posts', $postModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Post::$responseBody));
//
//
//        $postId     = json_decode($postResponse->getContent(), true)['data']['id'];
//
//        $commentModel      = factory(Comment::class)->make()->toArray();
//
//        $commentPostResponse = $this->json('POST', 'api/v1/user/posts/'.$postId.'/comments',  $commentModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Comment::$responseBody))
//            ->assertJson(array("data"=>$commentModel));
//
//        $commentId         = json_decode($commentPostResponse->getContent(), true)['data']['id'];
//
//        $commentModel      = factory(Comment::class)->make()->toArray();
//
//        $commentModel['commentText'] = '';
//
//        $this->json('PUT', 'api/v1/user/posts/'.$postId.'/comments/'. $commentId, $commentModel, $this->httpHeader)
//            ->assertStatus(400);
//
//        $this->cleanupDatabaseOfTestCommentPost($postId, $commentId);
//
//        $this->cleanupDatabaseOfTestPost($postId, $profileId);
//
//        $this->cleanupDatabaseOfTestProfile($profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "COMMENT-POST: canNotUpdateCommentPostWithoutName: PASSED \n";
//    }
//
//
//    /** @test canUpdateCommentPost */
//    public function canUpdateCommentPost()
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
//        $postModel             = factory(Post::class)->make()->toArray();
//
//        $postResponse          = $this->json('POST', 'api/v1/user/posts', $postModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Post::$responseBody));
//
//        $postId     = json_decode($postResponse->getContent(), true)['data']['id'];
//
//        $commentAModel      = factory(Comment::class)->make()->toArray();
//
//        $commentPostResponse = $this->json('POST', 'api/v1/user/posts/'.$postId.'/comments',  $commentAModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Comment::$responseBody))
//            ->assertJson(array("data"=>$commentAModel));
//
//        $commentId         = json_decode($commentPostResponse->getContent(), true)['data']['id'];
//
//        $commentBModel      = factory(Comment::class)->make()->toArray();
//
//        $this->json('PUT', 'api/v1/user/posts/'.$postId.'/comments/'. $commentId, $commentBModel, $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Comment::$responseBody))
//            ->assertJson(array("data"=>$commentBModel));
//
//        $this->cleanupDatabaseOfTestCommentPost($postId, $commentId);
//
//        $this->cleanupDatabaseOfTestPost($postId, $profileId);
//
//        $this->cleanupDatabaseOfTestProfile($profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "COMMENT-POST: canUpdateCommentPost: PASSED \n";
//    }
//
//    /** @test canDeleteCommentPost */
//    public function canDeleteCommentPost()
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
//        $postModel      = factory(Post::class)->make()->toArray();
//
//        $postResponse   = $this->json('POST', 'api/v1/user/posts', $postModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Post::$responseBody));
//
//        $postId         = json_decode($postResponse->getContent(), true)['data']['id'];
//
//        $commentModel      = factory(Comment::class)->make()->toArray();
//
//        $commentPostResponse = $this->json('POST', 'api/v1/user/posts/'.$postId.'/comments',  $commentModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Comment::$responseBody))
//            ->assertJson(array("data"=>$commentModel));
//
//        $commentId         = json_decode($commentPostResponse->getContent(), true)['data']['id'];
//
//        $this->json('DELETE', 'api/v1/user/posts/'.$postId.'/comments/'. $commentId, [], $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Comment::$responseBody))
//            ->assertJson(array("data"=>$commentModel));
//
//        $this->cleanupDatabaseOfTestPost($postId, $profileId);
//
//        $this->cleanupDatabaseOfTestProfile($profileId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "COMMENT-POST: canDeleteCommentPost: PASSED \n";
//    }
//
//    // Clean up function for comment posts
//    public function cleanupDatabaseOfTestCommentPost(int $postId, int $commentId) {
//        $this->json('DELETE', 'api/v1/user/posts/'.$postId.'/comments/'.$commentId, [], $this->httpHeader)->assertStatus(200);
//    }
//
//    // Clean up function for posts
//    public function cleanupDatabaseOfTestPost(int $postId, int $profileId) {
//        $this->json('DELETE', 'api/v1/user/posts/'. $postId, [], $this->httpHeader)->assertStatus(200);
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
