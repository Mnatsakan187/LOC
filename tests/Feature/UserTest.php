<?php

namespace Tests\Feature;

use App\Exceptions\Handler;
use App\User;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;


class UserTest extends TestCase
{
    use WithFaker;
    protected $user;
    protected $httpHeader;
    protected $cleanUpAfterTests;

    /*
     * canNotCreateUserWithoutName
     * canNotCreateUserWithSameEmail
     * canCreateNewUser
     * canNotLoginWithoutEmail
     * canLogin
     * canNotUpdateWithoutFirsName
     * canUpdateUser
     * canDeleteUser
     */

    protected function setUp():void
    {
        /**
         * This disables the exception handling to display the stacktrace on the console
         * the same way as it shown on the browser
         */
        parent::setUp();
        $this->disableExceptionHandling();
    }

    protected function disableExceptionHandling()
    {
        $this->app->instance(ExceptionHandler::class, new class extends Handler {
            public function __construct() {}

            public function report(\Exception $e)
            {
                // no-op
            }

            public function render($request, \Exception $e) {
                throw $e;
            }
        });
    }

    /** @test canNotCreateUserWithoutName */
    public function canNotCreateUserWithoutEmail()
    {
        $userModel         = factory(User::class)->make()->toArray();
        $userModel['test'] = 1;

        $userModel['email'] = '';

        $this->json('POST', 'api/v1/user', $userModel)
            ->assertStatus(400);

        echo "USER: canNotCreateUserWithoutName: PASSED \n";
    }

    /** @test canNotCreateUserWithSameEmail */
    public function canNotCreateUserWithSameEmail()
    {
        $userAModel         = factory(User::class)->make()->toArray();
        $userAModel['test'] = 1;

        $userResponse = $this->json('POST', 'api/v1/user', $userAModel)
            ->assertStatus(201);

        $userId     = json_decode($userResponse->getContent(), true)['data']['id'];

        $userEmail  = json_decode($userResponse->getContent(), true)['data']['email'];

        $user       = User::findOrFail($userId);
        $emailToken = $user->email_token;

        $this->json('GET', 'api/v1/user/validate/' . $emailToken, $userAModel)
            ->assertStatus(202);

        // Login user to the system
        $credentials = [
            'email'     => $user->email,
            'password'  => 123456
        ];

        $userLoginResponse       = $this->json('POST', 'api/v1/user/login' , $credentials)
            ->assertStatus(202);

        $userBModel         = factory(User::class)->make()->toArray();

        $userBModel['email'] = $userEmail;

        $this->json('POST', 'api/v1/user', $userBModel)
            ->assertStatus(400);

        $this->cleanupDatabaseOfTestData($userLoginResponse);

        echo "USER: canNotCreateUserWithSameEmail: PASSED \n";
    }

    /** @test canCreateNewUser */
    public function canCreateNewUser()
    {
        $userAModel         = factory(User::class)->make()->toArray();

        $userAModel['test'] = 1;

        $userResponse = $this->json('POST', 'api/v1/user', $userAModel)
            ->assertStatus(201);

        $userId     = json_decode($userResponse->getContent(), true)['data']['id'];

        $user       = User::findOrFail($userId);
        $emailToken = $user->email_token;


        $this->json('GET', 'api/v1/user/validate/' . $emailToken, $userAModel)
            ->assertStatus(202);

        // Login user to the system
        $credentials = [
            'email'     => $user->email,
            'password'  => 123456
        ];

        $userLoginResponse       = $this->json('POST', 'api/v1/user/login' , $credentials)
            ->assertStatus(202);

        $this->cleanupDatabaseOfTestData($userLoginResponse);

        echo "USER: canCreateNewUser: PASSED \n";
    }

    /** @test canNotLoginWithoutEmail */
    public function canNotLoginWithoutEmail()
    {
        $userAModel         = factory(User::class)->make()->toArray();

        $userAModel['test'] = 1;

        $userResponse = $this->json('POST', 'api/v1/user', $userAModel)
            ->assertStatus(201);

        $userId     = json_decode($userResponse->getContent(), true)['data']['id'];

        $user       = User::findOrFail($userId);
        $emailToken = $user->email_token;

        $this->json('GET', 'api/v1/user/validate/' . $emailToken, $userAModel)
            ->assertStatus(202);

        // Login user to the system
        $credentials = [
            'email'     => '',
            'password'  => 123456
        ];

        $this->json('POST', 'api/v1/user/login' , $credentials)
            ->assertStatus(400);

        // Login user to the system
        $credentials = [
            'email'     => $user->email,
            'password'  => 123456
        ];

        $userLoginResponse       = $this->json('POST', 'api/v1/user/login' , $credentials)
            ->assertStatus(202);

        $this->cleanupDatabaseOfTestData($userLoginResponse);

        echo "USER: canNotLoginWithoutEmail: PASSED \n";
    }

    /** @test canLogin */
    public function canLogin()
    {
        $userAModel         = factory(User::class)->make()->toArray();

        $userAModel['test'] = 1;

        $userResponse = $this->json('POST', 'api/v1/user', $userAModel)
            ->assertStatus(201);

        $userId     = json_decode($userResponse->getContent(), true)['data']['id'];

        $user       = User::findOrFail($userId);
        $emailToken = $user->email_token;

        $this->json('GET', 'api/v1/user/validate/' . $emailToken, $userAModel)
            ->assertStatus(202);

        // Login user to the system
        $credentials = [
            'email'     => $user->email,
            'password'  => 123456
        ];

        $userLoginResponse       = $this->json('POST', 'api/v1/user/login' , $credentials)
            ->assertStatus(202);

        $this->cleanupDatabaseOfTestData($userLoginResponse);

        echo "USER: canLogin: PASSED \n";
    }

//    /** @test canNotUpdateWithoutFirsName */
//    public function canNotUpdateWithoutFirsName()
//    {
//        $userAModel         = factory(User::class)->make()->toArray();
//
//        $userAModel['test'] = 1;
//
//        $userResponse = $this->json('POST', 'api/v1/user', $userAModel)
//            ->assertStatus(201);
//
//
//        $userId     = json_decode($userResponse->getContent(), true)['data']['id'];
//
//        $user       = User::findOrFail($userId);
//        $emailToken = $user->email_token;
//
//        // Validate the new user
//        $userValidateResponse       = $this->json('GET', 'api/v1/user/validate/' . $emailToken, $userAModel)
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
//        $userBModel    = factory(User::class)->make()->toArray();
//
//        $userBModel['firstName']  = '';
//
//        $this->json('PUT', 'api/v1/user', $userBModel, [
//            'Accept'        => 'application/json',
//            'Content-Type'  => 'application/json',
//            'Authorization' => 'Bearer ' . $userLoginResponse->headers->get('token')
//        ])->assertStatus(400);
//
//        $this->cleanupDatabaseOfTestData($userLoginResponse);
//
//        echo "USER: canNotUpdateWithoutFirsName: PASSED \n";
//    }

//    /** @test canUpdateUser */
//    public function canUpdateUser()
//    {
//        $userAModel         = factory(User::class)->make()->toArray();
//
//        $userAModel['test'] = 1;
//
//
//
//        $userResponse = $this->json('POST', 'api/v1/user/update', $userAModel)
//            ->assertStatus(201);
//
//        $userId     = json_decode($userResponse->getContent(), true)['data']['id'];
//
//        $user       = User::findOrFail($userId);
//        $emailToken = $user->email_token;
//
//        // Validate the new user
//        $userValidateResponse       = $this->json('GET', 'api/v1/user/validate/' . $emailToken, $userAModel)
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
//        $userBModel    = factory(User::class)->make()->toArray();
//
//        $this->json('PUT', 'api/v1/user', $userBModel, [
//            'Accept'        => 'application/json',
//            'Content-Type'  => 'application/json',
//            'Authorization' => 'Bearer ' . $userLoginResponse->headers->get('token')
//        ])->assertStatus(200);
//
//        $this->cleanupDatabaseOfTestData($userLoginResponse);
//
//        echo "USER: canUpdateUser: PASSED \n";
//    }

    /** @test canDeleteUser */
    public function canDeleteUser()
    {
        $userAModel         = factory(User::class)->make()->toArray();

        $userAModel['test'] = 1;

        $userResponse = $this->json('POST', 'api/v1/user', $userAModel)
            ->assertStatus(201);

        $userId     = json_decode($userResponse->getContent(), true)['data']['id'];

        $user       = User::findOrFail($userId);
        $emailToken = $user->email_token;

        // Validate the new user
        $userValidateResponse       = $this->json('GET', 'api/v1/user/validate/' . $emailToken, $userAModel)
            ->assertStatus(202);

        // Login user to the system
        $credentials = [
            'email'     => $user->email,
            'password'  => 123456
        ];

        $userLoginResponse       = $this->json('POST', 'api/v1/user/login' , $credentials)
            ->assertStatus(202);

        $this->json('DELETE', 'api/v1/user',[], $this->httpHeader = [
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $userLoginResponse->headers->get('token')
        ])->assertStatus(410);

        echo "USER: canDeleteUser: PASSED \n";
    }

    // Clean up function for user data
    public function cleanupDatabaseOfTestData($userLoginResponse) {
        $this->json('DELETE', 'api/v1/user',[], $this->httpHeader = [
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $userLoginResponse->headers->get('token')
        ])->assertStatus(410);
    }


}
