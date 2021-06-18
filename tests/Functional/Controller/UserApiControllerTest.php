<?php

namespace App\Tests\Functional\Controller;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserApiControllerTest extends WebTestCase
{
    public const API_URL = 'http://api.dev';
    private Client $httpClient;

    public function setUp(): void
    {
        $this->httpClient = new Client(
            [
                'cookies' => true,
                'base_uri' => self::API_URL,
                'http_errors' => false
            ]
        );
    }

    /**
     * @test
     * @throws GuzzleException
     */
    public function whenAccessToApiUserItReturnsUnauthorized()
    {
        $response = $this->httpClient->request('POST', '/api/addUser');

        $this->httpClient->post('/api/addUser');

        $this->assertEquals(401, $response->getStatusCode());
    }

    /**
     * @test
     * @throws GuzzleException
     */
    public function whenAccessToApiUserWithoutBodyReturnsNoContent()
    {
        $this->httpClient = new Client
        (
            [
                'base_uri' => 'http://api.dev',
                'auth' => ['admin2', 'admin'],
            ]
        );
        $response = $this->httpClient->request('POST', '/api/addUser');


        $this->assertEquals(204, $response->getStatusCode());
    }

    /**
     * @test
     * @throws GuzzleException
     */
    public function WhenAccessToApiUserWithContentAndAdminRoleMustReturns201()
    {
        $this->httpClient = new Client
        (
            [
                'base_uri' => self::API_URL,
                'auth' => ['admin2', 'admin'],
            ]
        );

        $response = $this->httpClient->request(
            'POST',
            '/api/addUser',
            [
                'headers' => ['Content-Type' => 'application/json'],
                'json' => [
                    'name' => 'name',
                    'username' => 'user',
                    'password' => 'admin',
                    'roles' => 'ROLE_ADMIN',
                ]
            ]
        );

        $this->assertEquals(201, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function CreateUserOk()
    {
        $this->httpClient = new Client
        (
            [
                'base_uri' => self::API_URL,
                'auth' => ['admin2', 'admin'],
            ]
        );

        $response = $this->httpClient->request(
            'POST',
            '/api/addUser',
            [
                'headers' => ['Content-Type' => 'application/json'],
                'json' => [
                    'name' => 'name',
                    'username' => 'user',
                    'password' => 'admin',
                    'roles' => 'ROLE_ADMIN',
                ]
            ]
        );

        $this->assertEquals('{"status":"New User created"}', $response->getBody()->getContents());
    }

    /**
     * @test
     * @throws GuzzleException
     */
    public function updateUserOk()
    {
        $this->httpClient = new Client
        (
            [
                'base_uri' => self::API_URL,
                'auth' => ['admin2', 'admin'],
                'json' => [
                    'password' => 'ROLE_PAGE_2',
                ]
            ]
        );

        $response = $this->httpClient->request('PUT', '/api/updateUser/name');

        $this->assertEquals('{"status":"User updated"}', $response->getBody()->getContents());
    }

    /**
     * @test
     * @throws GuzzleException
     */
    public function deleteUserOk()
    {
        $this->httpClient = new Client
        (
            [
                'base_uri' => self::API_URL,
                'auth' => ['admin2', 'admin'],
            ]
        );

        $response = $this->httpClient->request('DELETE', '/api/removeUser/name');

        $this->assertEquals('{"status":"User deleted"}', $response->getBody()->getContents());
    }

}