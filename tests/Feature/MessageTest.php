<?php
//
//namespace Tests\Feature;
//
//use App\Message;
//use App\Exceptions\Handler;
//use App\User;
//use Illuminate\Contracts\Debug\ExceptionHandler;
//use Tests\TestCase;
//use Illuminate\Foundation\Testing\WithFaker;
//
//
//class MessageTest extends TestCase
//{
//    use WithFaker;
//    protected $user;
//    protected $httpHeader;
//    protected $cleanUpAfterTests;
//
//    /*
//     * canGetListOfMessage
//     * canNotCreateMessageWithoutMessage
//     * canCreateNewMessage
//     * canNotUpdateMessageWithoutMessage
//     * canUpdateMessage
//     * canDeleteMessage
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
//    /** @test canGetListOfMessage */
//    public function canGetListOfMessage()
//    {
//        $messageModel             = factory(Message::class)->make()->toArray();
//
//        $messageResponse          = $this->json('POST', 'api/v1/user/messages', $messageModel, $this->httpHeader)
//            ->assertStatus(201);
//
//        $toUserId     = json_decode($messageResponse->getContent(), true)['data']['toUserId'];
//        $messageId    = json_decode($messageResponse->getContent(), true)['data']['id'];
//        $this->json('GET', 'api/v1/user/messages/'.$toUserId, [], $this->httpHeader)
//            ->assertStatus(200);
//
//        $this->cleanupDatabaseOfTestMessage($messageId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "MESSAGE: canGetListOfMessage: PASSED \n";
//    }
//
//    /** @test canNotCreateMessageWithoutMessage */
//    public function canNotCreateMessageWithoutMessage()
//    {
//        $messageModel             = factory(Message::class)->make()->toArray();
//
//        $messageModel['message'] = '';
//
//        $this->json('POST', 'api/v1/user/messages', $messageModel, $this->httpHeader)
//            ->assertStatus(400);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "MESSAGE: canNotCreateMessageWithoutMessage: PASSED \n";
//    }
//
//    /** @test canCreateNewMessage */
//    public function canCreateNewMessage()
//    {
//        $messageModel             = factory(Message::class)->make()->toArray();
//
//        $messageResponse = $this->json('POST', 'api/v1/user/messages', $messageModel, $this->httpHeader)
//            ->assertStatus(201);
//
//        $messageId         = json_decode($messageResponse->getContent(), true)['data']['id'];
//
//        $this->cleanupDatabaseOfTestMessage($messageId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "MESSAGE: canCreateNewMessage: PASSED \n";
//    }
//
//    /** @test canNotUpdateMessageWithoutMessage */
//    public function canNotUpdateMessageWithoutMessage()
//    {
//        $messageModel             = factory(Message::class)->make()->toArray();
//
//        $messageResponse = $this->json('POST', 'api/v1/user/messages', $messageModel, $this->httpHeader)
//            ->assertStatus(201);
//
//        $messageId         = json_decode($messageResponse->getContent(), true)['data']['id'];
//
//        $messageModel['message'] = '';
//
//        $this->json('PUT', 'api/v1/user/messages/'. $messageId, $messageModel, $this->httpHeader)
//            ->assertStatus(400);
//
//        $this->cleanupDatabaseOfTestMessage($messageId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "MESSAGE: canCreateNewMessage: PASSED \n";
//    }
//
//    /** @test canUpdateMessage */
//    public function canUpdateMessage()
//    {
//        $messageModel             = factory(Message::class)->make()->toArray();
//
//        $messageResponse = $this->json('POST', 'api/v1/user/messages', $messageModel, $this->httpHeader)
//            ->assertStatus(201);
//
//        $messageId         = json_decode($messageResponse->getContent(), true)['data']['id'];
//
//        $this->json('PUT', 'api/v1/user/messages/'. $messageId, $messageModel, $this->httpHeader)
//            ->assertStatus(200);
//
//        $this->cleanupDatabaseOfTestMessage($messageId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "MESSAGE: canUpdateMessage: PASSED \n";
//    }
//
//    /** @test canDeleteMessage*/
//    public function canDeleteMessage()
//    {
//        $messageModel             = factory(Message::class)->make()->toArray();
//
//        $messageResponse = $this->json('POST', 'api/v1/user/messages', $messageModel, $this->httpHeader)
//            ->assertStatus(201);
//
//        $messageId         = json_decode($messageResponse->getContent(), true)['data']['id'];
//
//        $this->json('DELETE', 'api/v1/user/messages/'. $messageId, [], $this->httpHeader)
//            ->assertStatus(200);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "MESSAGE: canDeleteMessage: PASSED \n";
//    }
//
//    // Clean up function for message
//    public function cleanupDatabaseOfTestMessage(int $messageId) {
//        $this->json('DELETE', 'api/v1/user/messages/'. $messageId, [], $this->httpHeader)->assertStatus(200);
//    }
//
//    // Clean up function for user
//    public function cleanupDatabaseOfTestDataUser() {
//        $this->json('DELETE', 'api/v1/user',[], $this->httpHeader)->assertStatus(410);
//    }
//
//}
