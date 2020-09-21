<?php
//
//namespace Tests\Feature;
//
//use App\Exceptions\Handler;
//use App\Group;
//use App\User;
//use Illuminate\Contracts\Debug\ExceptionHandler;
//use Tests\TestCase;
//use Illuminate\Foundation\Testing\WithFaker;
//
//
//class GroupTest extends TestCase
//{
//    use WithFaker;
//    protected $user;
//    protected $httpHeader;
//    protected $cleanUpAfterTests;
//
//    /*
//     * canGetListOfGroup
//     * canNotCreateGroupWithoutName
//     * canNotCreateGroupWithSameName
//     * canCreateNewGroup
//     * canGetSingleGroup
//     * canNotEditGroupWithoutName
//     * canEditGroup
//     * canDeleteGroup
//     * canNotCreateGroupWithExistName
//     * canNotUpdateGroupWithExistName
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
//    /** @test canGetListOfGroup*/
//    public function canGetListOfGroup()
//    {
//        $groupModel             = factory(Group::class)->make()->toArray();
//
//        $groupResponse          = $this->json('POST', 'api/v1/user/groups', $groupModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Group::$responseBody));
//
//        $groupId     = json_decode($groupResponse->getContent(), true)['data']['id'];
//
//        $this->json('GET', 'api/v1/user/groups', [], $this->httpHeader)
//            ->assertStatus(200);
//
//        $this->cleanupDatabaseOfTestGroup($groupId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "GROUP: canGetListOfGroup: PASSED \n";
//    }
//
//    /** @test canNotCreateGroupWithoutName */
//    public function canNotCreateGroupWithoutName()
//    {
//        $groupModel         = factory(Group::class)->make()->toArray();
//
//        $groupModel['name'] = '';
//
//        $this->json('POST', 'api/v1/user/groups', $groupModel, $this->httpHeader)
//            ->assertStatus(400);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "GROUP: canNotCreateGroupWithoutName: PASSED \n";
//    }
//
//    /** @test canNotCreateGroupWithExistName */
//    public function canNotCreateGroupWithExistName()
//    {
//        $groupModel         = factory(Group::class)->make()->toArray();
//
//        $groupResponse = $this->json('POST', 'api/v1/user/groups', $groupModel, $this->httpHeader)
//            ->assertStatus(201);
//
//        $groupId         = json_decode($groupResponse->getContent(), true)['data']['id'];
//        $groupName       = json_decode($groupResponse->getContent(), true)['data']['name'];
//
//        $groupModel['name'] = $groupName;
//        $this->json('POST', 'api/v1/user/groups', $groupModel, $this->httpHeader)
//            ->assertStatus(400);
//
//        $this->cleanupDatabaseOfTestGroup($groupId);
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "GROUP: canNotCreateGroupWithExistName: PASSED \n";
//    }
//
//    /** @test canCreateNewGroup */
//    public function canCreateNewGroup()
//    {
//        $groupModel      = factory(Group::class)->make()->toArray();
//
//        $groupResponse   = $this->json('POST', 'api/v1/user/groups', $groupModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Group::$responseBody));
//
//        $groupId         = json_decode($groupResponse->getContent(), true)['data']['id'];
//
//        $this->cleanupDatabaseOfTestGroup($groupId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "GROUP: canCreateNewGroup: PASSED \n";
//    }
//
//    /** @test canGetSingleGroup */
//    public function canGetSingleGroup()
//    {
//        $groupModel             = factory(Group::class)->make()->toArray();
//
//        $groupResponse          = $this->json('POST', 'api/v1/user/groups', $groupModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Group::$responseBody));
//
//        $groupId     = json_decode($groupResponse->getContent(), true)['data']['id'];
//
//        $this->json('GET', 'api/v1/user/groups/'. $groupId, [], $this->httpHeader)
//            ->assertStatus(200);
//
//        $this->cleanupDatabaseOfTestGroup($groupId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "GROUP: canGetSingleGroup: PASSED \n";
//    }
//
//    /** @test canNotUpdateGroupWithoutName */
//    public function canNotUpdateGroupWithoutName()
//    {
//        $groupModel             = factory(Group::class)->make()->toArray();
//
//        $groupResponse          = $this->json('POST', 'api/v1/user/groups', $groupModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Group::$responseBody));
//
//        $groupId     = json_decode($groupResponse->getContent(), true)['data']['id'];
//
//        $groupModel['name'] = '';
//
//        $this->json('PUT', 'api/v1/user/groups/'. $groupId, $groupModel, $this->httpHeader)
//            ->assertStatus(400);
//
//        $this->cleanupDatabaseOfTestGroup($groupId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "GROUP: canNotUpdateGroupWithoutName: PASSED \n";
//    }
//
////    /** @test canNotUpdateGroupWithExistName */
////    public function canNotUpdateGroupWithExistName()
////    {
////        $groupModel        = factory(Group::class)->make()->toArray();
////
////        $groupResponse     = $this->json('POST', 'api/v1/user/groups', $groupModel, $this->httpHeader)
////            ->assertStatus(201)
////            ->assertJsonStructure(array("data"=>Group::$responseBody));
////
////        $groupId     = json_decode($groupResponse->getContent(), true)['data']['id'];
////        $groupName   = json_decode($groupResponse->getContent(), true)['data']['name'];
////
////        $groupModel['name'] = $groupName;
////
////        $this->json('PUT', 'api/v1/user/groups/'. $groupId, $groupModel, $this->httpHeader)
////            ->assertStatus(400);
////
////        $this->cleanupDatabaseOfTestGroup($groupId);
////
////        $this->cleanupDatabaseOfTestDataUser();
////
////        echo "GROUP: canNotUpdateGroupWithExistName: PASSED \n";
////    }
//
//
//
//    /** @test canUpdateGroup */
//    public function canUpdateGroup()
//    {
//        $groupAModel             = factory(Group::class)->make()->toArray();
//
//        $groupResponse          = $this->json('POST', 'api/v1/user/groups', $groupAModel, $this->httpHeader)
//            ->assertStatus(201)
//            ->assertJsonStructure(array("data"=>Group::$responseBody));
//
//        $groupId     = json_decode($groupResponse->getContent(), true)['data']['id'];
//
//        $groupBModel             = factory(Group::class)->make()->toArray();
//
//        $this->json('PUT', 'api/v1/user/groups/'. $groupId, $groupBModel, $this->httpHeader)
//            ->assertStatus(200)
//            ->assertJsonStructure(array("data"=>Group::$responseBody));
//
//        $this->cleanupDatabaseOfTestGroup($groupId);
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "GROUP: canUpdateGroup: PASSED \n";
//    }
//
//
//
//    /** @test canDeleteGroup */
//    public function canDeleteGroup()
//    {
//        $groupModel             = factory(Group::class)->make()->toArray();
//
//        $groupResponse          = $this->json('POST', 'api/v1/user/groups', $groupModel, $this->httpHeader)
//            ->assertStatus(201);
//
//        $groupId     = json_decode($groupResponse->getContent(), true)['data']['id'];
//
//        $this->json('DELETE', 'api/v1/user/groups/'. $groupId, [], $this->httpHeader)
//            ->assertJsonStructure(array("data"=>Group::$responseBody));
//
//        $this->cleanupDatabaseOfTestDataUser();
//
//        echo "GROUP: canDeleteGroup: PASSED \n";
//    }
//
//    // Clean up function for Group
//    public function cleanupDatabaseOfTestGroup(int $groupId) {
//        $this->json('DELETE', 'api/v1/user/groups/'. $groupId, [], $this->httpHeader)->assertStatus(200);
//
//    }
//
//    // Clean up function for user
//    public function cleanupDatabaseOfTestDataUser() {
//        $this->json('DELETE', 'api/v1/user',[], $this->httpHeader)->assertStatus(410);
//    }
//
//}
