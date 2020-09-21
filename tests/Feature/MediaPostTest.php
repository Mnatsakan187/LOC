<?php
//
//namespace Tests\Feature;
//
//use App\Exceptions\Handler;
//use App\Post;
//use App\Media;
//use App\User;
//use Illuminate\Contracts\Debug\ExceptionHandler;
//use Tests\TestCase;
//use Illuminate\Foundation\Testing\WithFaker;
//
//
//class MediaPostTest extends TestCase
//{
//    use WithFaker;
//    protected $user;
//    protected $httpHeader;
//    protected $cleanUpAfterTests;
//
//    /*
//     * canGetListOfMediaPost
//     * canNotCreateMediaPostWithoutName
//     * canNotCreateMediaPostWithExistName
//     * canCreateMediaPost
//     * canGetSingleMediaPost
//     * canNotUpdateMediaPostWithoutName
//     * canNotUpdateMediaPostWithExistName
//     * canUpdateMediaPost
//     * canDeleteMediaPost
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
//    /** @test canGetListOfMediaPost */
//    public function canGetListOfMediaPost()
//    {
//        $postModel             = factory(Post::class)->make()->toArray();
//
//        $postResponse          = $this->json('POST', 'api/v1/user/posts', $postModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Post::$responseBody))
//            ->assertJson(array("data"=>$postModel));
//
//
//
//        $postId     = json_decode($postResponse->getContent(), true)['data']['id'];
//
//        $mediaModel      = factory(Media::class)->make()->toArray();
//
//
//        $mediaPostResponse = $this->json('POST', 'api/v1/user/posts/'.$postId.'/media',  $mediaModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Media::$responseBody))
//            ->assertJson(array("data"=>$mediaModel));
//
//
//
//        $this->json('GET', 'api/v1/user/posts/'.$postId.'/media', [], $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>['0' => Media::$responseBody]))
//            ->assertJson(array("data"=>['0' => $mediaModel]));
//
//        $mediaId     = json_decode($mediaPostResponse->getContent(), true)['data']['id'];
//
//        $this->cleanupDatabaseOfTestMediaPost($postId, $mediaId);
//
//        $this->cleanupDatabaseOfTestPost($postId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "MEDIA-POST: canGetListOfMediaPost: PASSED \n";
//    }
//
//    /** @test canNotCreateMediaPostWithoutName */
//    public function canNotCreateMediaPostWithoutName()
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
//        $mediaModel      = factory(Media::class)->make()->toArray();
//
//        $mediaModel['displayName'] = '';
//
//        $this->json('POST', 'api/v1/user/posts/'.$postId.'/media',  $mediaModel,  $this->httpHeader)
//            ->assertStatus(400);
//
//        $this->cleanupDatabaseOfTestPost($postId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "MEDIA-POST: canNotCreateMediaPostWithoutName: PASSED \n";
//    }
//
//
//
//    /** @test canCreateMediaPost */
//    public function canCreateMediaPost()
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
//        $mediaModel      = factory(Media::class)->make()->toArray();
//
//        $mediaPostResponse = $this->json('POST', 'api/v1/user/posts/'.$postId.'/media',  $mediaModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Media::$responseBody))
//            ->assertJson(array("data"=>$mediaModel));
//
//        $mediaId         = json_decode($mediaPostResponse->getContent(), true)['data']['id'];
//
//        $this->cleanupDatabaseOfTestMediaPost($postId, $mediaId);
//
//        $this->cleanupDatabaseOfTestPost($postId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "MEDIA-POST: canCreateMediaPost: PASSED \n";
//    }
//
//    /** @test canGetSingleMediaPost */
//    public function canGetSingleMediaPost()
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
//        $mediaModel      = factory(Media::class)->make()->toArray();
//
//        $mediaPostResponse = $this->json('POST', 'api/v1/user/posts/'.$postId.'/media',  $mediaModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Media::$responseBody))
//            ->assertJson(array("data"=>$mediaModel));
//
//        $mediaId         = json_decode($mediaPostResponse->getContent(), true)['data']['id'];
//
//        $this->json('GET', 'api/v1/user/posts/'.$postId.'/media/'.$mediaId, [], $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Media::$responseBody))
//            ->assertJson(array("data"=>$mediaModel));
//
//        $this->cleanupDatabaseOfTestMediaPost($postId, $mediaId);
//
//        $this->cleanupDatabaseOfTestPost($postId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "MEDIA-POST: canGetSingleMediaPost: PASSED \n";
//    }
//
//    /** @test canNotUpdateMediaPostWithoutName */
//    public function canNotUpdateMediaPostWithoutName()
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
//        $mediaModel      = factory(Media::class)->make()->toArray();
//
//        $mediaPostResponse = $this->json('POST', 'api/v1/user/posts/'.$postId.'/media',  $mediaModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Media::$responseBody))
//            ->assertJson(array("data"=>$mediaModel));
//
//        $mediaId         = json_decode($mediaPostResponse->getContent(), true)['data']['id'];
//
//        $mediaModel      = factory(Media::class)->make()->toArray();
//
//        $mediaModel['displayName'] = '';
//
//        $this->json('PUT', 'api/v1/user/posts/'.$postId.'/media/'. $mediaId, $mediaModel, $this->httpHeader)
//            ->assertStatus(400);
//
//        $this->cleanupDatabaseOfTestMediaPost($postId, $mediaId);
//
//        $this->cleanupDatabaseOfTestPost($postId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "MEDIA-POST: canNotUpdateMediaPostWithoutName: PASSED \n";
//    }
//
//
//    /** @test canUpdateMediaPost */
//    public function canUpdateMediaPost()
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
//        $mediaAModel      = factory(Media::class)->make()->toArray();
//
//        $mediaPostResponse = $this->json('POST', 'api/v1/user/posts/'.$postId.'/media',  $mediaAModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Media::$responseBody))
//            ->assertJson(array("data"=>$mediaAModel));
//
//        $mediaId         = json_decode($mediaPostResponse->getContent(), true)['data']['id'];
//
//        $mediaBModel      = factory(Media::class)->make()->toArray();
//
//        $this->json('PUT', 'api/v1/user/posts/'.$postId.'/media/'. $mediaId, $mediaBModel, $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Media::$responseBody))
//            ->assertJson(array("data"=>$mediaBModel));
//
//        $this->cleanupDatabaseOfTestMediaPost($postId, $mediaId);
//
//        $this->cleanupDatabaseOfTestPost($postId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "MEDIA-POST: canUpdateMediaPost: PASSED \n";
//    }
//
//    /** @test canDeleteMediaPost */
//    public function canDeleteMediaPost()
//    {
//        $postModel      = factory(Post::class)->make()->toArray();
//
//        $postResponse   = $this->json('POST', 'api/v1/user/posts', $postModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Post::$responseBody));
////            ->assertJson(array("data"=>$postModel));
//
//        $postId          = json_decode($postResponse->getContent(), true)['data']['id'];
//
//        $mediaModel      = factory(Media::class)->make()->toArray();
//
//        $mediaPostResponse = $this->json('POST', 'api/v1/user/posts/'.$postId.'/media',  $mediaModel,  $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Media::$responseBody))
//            ->assertJson(array("data"=>$mediaModel));
//
//        $mediaId         = json_decode($mediaPostResponse->getContent(), true)['data']['id'];
//
//        $this->json('DELETE', 'api/v1/user/posts/'.$postId.'/media/'. $mediaId, [], $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Media::$responseBody))
//            ->assertJson(array("data"=>$mediaModel));
//
//        $this->cleanupDatabaseOfTestPost($postId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "MEDIA-POST: canDeleteMediaPost: PASSED \n";
//    }
//
//    // Clean up function for media post
//    public function cleanupDatabaseOfTestMediaPost(int $postId, int $mediaId) {
//        $this->json('DELETE', 'api/v1/user/posts/'.$postId.'/media/'.$mediaId, [], $this->httpHeader)->assertStatus(200);
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
