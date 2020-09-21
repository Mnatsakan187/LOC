<?php

namespace Tests\Feature;

use App\Exceptions\Handler;
use App\User;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;


class NetworkTest extends TestCase
{
    use WithFaker;
    protected $user;
    protected $httpHeader;
    protected $cleanUpAfterTests;

    /*
     * canGetListOfNetwork
     * cannotCreateNetwork
     * canCreateNewNetwork
     * canGetSingleNetwork
     * canDeleteNetwork
     */

    protected function setUp():void
    {
        /**
         * This disables the exception handling to display the stacktrace on the console
         * the same way as it shown on the browser
         */
        parent::setUp();
        $this->disableExceptionHandling();
        $this->getUserForTesting();
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

    protected function getUserForTesting()
    {
        $userModel       = factory(User::class)->make()->toArray();

        $userModel['test'] = 1;

        // Register a new user
        $userResponse       = $this->json('POST', 'api/v1/user/', $userModel)
            ->assertStatus(201);

        $userId     = json_decode($userResponse->getContent(), true)['data']['id'];

        $user       = User::findOrFail($userId);
        $emailToken = $user->email_token;

        // Validate the new user
        $userValidateResponse       = $this->json('GET', 'api/v1/user/validate/' . $emailToken, $userModel)
            ->assertStatus(202);

        // Login user to the system
        $credentials = [
            'email'     => $user->email,
            'password'  => 123456
        ];

        $userLoginResponse       = $this->json('POST', 'api/v1/user/login' , $credentials)
            ->assertStatus(202);

        $this->user = $user;

        $this->httpHeader = [
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $userLoginResponse->headers->get('token')
        ];
    }

    /** @test canGetListOfNetwork */
    public function canGetListOfNetwork()
    {

        $networkResponse   = $this->json('POST', 'api/v1/user/networks',
            ['networkUserId' =>[['id'=>$this->user->id]] ], $this->httpHeader)
            ->assertStatus(201);


        $networkUserId         = json_decode($networkResponse->getContent(), true)['data']['id'];

        $this->json('GET', 'api/v1/user/networks', [], $this->httpHeader)
            ->assertStatus(200);

        $this->cleanupDatabaseOfNetwork($networkUserId);


        $this->cleanupDatabaseOfTestDataUser();

        echo "NETWORK: canGetListOfNetwork: PASSED \n";
    }

    /** @test cannotCreateNetworkWithoutUserId */
    public function cannotCreateNetworkWithoutUserId()
    {

        $this->json('POST', 'api/v1/user/networks',
            ['networkUserId' =>[['id'=>'']] ], $this->httpHeader)
            ->assertStatus(400);

        $this->cleanupDatabaseOfTestDataUser();

        echo "NETWORK: cannotCreateNetworkWithoutUserId: PASSED \n";
    }

    /** @test canCreateNewNetwork */
    public function canCreateNewNetwork()
    {
        $networkResponse   = $this->json('POST', 'api/v1/user/networks',
            ['networkUserId' =>[['id'=>$this->user->id]] ], $this->httpHeader)
            ->assertStatus(201);

        $networkUserId         = json_decode($networkResponse->getContent(), true)['data']['id'];


        $this->cleanupDatabaseOfNetwork($networkUserId);

        $this->cleanupDatabaseOfTestDataUser();

        echo "NETWORK: canCreateNewNetwork: PASSED \n";
    }

    /** @test canGetSingleNetwork */
    public function canGetSingleNetwork()
    {
        $networkResponse   = $this->json('POST', 'api/v1/user/networks',
            ['networkUserId' =>[['id'=>$this->user->id]] ], $this->httpHeader)
            ->assertStatus(201);

        $networkUserId         = json_decode($networkResponse->getContent(), true)['data']['id'];

        $this->json('GET', 'api/v1/user/networks'. $networkUserId, [], $this->httpHeader)
            ->assertStatus(200);


        $this->cleanupDatabaseOfNetwork($networkUserId);

        $this->cleanupDatabaseOfTestDataUser();

        echo "NETWORK: canGetSingleNetwork: PASSED \n";
    }

    /** @test canDeleteNetwork */
    public function canDeleteNetwork()
    {
        $networkResponse   = $this->json('POST', 'api/v1/user/networks',
            ['networkUserId' =>[['id'=>$this->user->id]] ], $this->httpHeader)
            ->assertStatus(201);

        $networkUserId         = json_decode($networkResponse->getContent(), true)['data']['id'];

        $this->json('DELETE', 'api/v1/user/networks/'.$networkUserId, [], $this->httpHeader)
            ->assertStatus(200);

        $this->cleanupDatabaseOfTestDataUser();

        echo "NETWORK: canDeleteNetwork: PASSED \n";
    }

    // Clean up function for team project
    public function cleanupDatabaseOfNetwork(int $netWorkId) {
        $this->json('DELETE', 'api/v1/user/networks/'.$netWorkId, [], $this->httpHeader)->assertStatus(200);
    }



    // Clean up function for user
    public function cleanupDatabaseOfTestDataUser() {
        $this->json('DELETE', 'api/v1/user',[], $this->httpHeader)->assertStatus(410);
    }


}
