<?php


namespace App\Tests\Functional\Controller;

use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{

    //TODO: need create basic auth
    private Client $http;
    public function setUp() :void
    {
        $this->http = new Client(['base_uri' => 'http://api.dev']);
    }
        public function testAddingUser()
        {

            $data =[

            ];

            $request = $this->http->post('/api/', $data, json_encode($data));
            $response = $request->send();

            $this->assertEquals(201, $response->getStatusCode());die;
            $data = json_decode($response->getBody(true), true);
        }
}