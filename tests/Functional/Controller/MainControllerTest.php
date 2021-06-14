<?php
namespace App\Tests\Functional\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;


class MainControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private UserRepository|null $userRepository;

    public function setUp() :void
    {
        $this->client = static::createClient();
        $this->client->catchExceptions(true);
        $this->userRepository = static::$container->get(UserRepository::class);
    }

    /**
     * @test
     */
        public function webAccesToPageOneWithNoLoginRedirectsToLogin()
        {
            $expectedStatus = 302;

            $this->client->request('GET', '/page/1');

            $this->assertResponseRedirects('/login');
            $this->assertEquals($expectedStatus, $this->client->getResponse()->getStatusCode());
        }

    /**
     * @test
     */
        public function webAccesToPageTwoWithNoLoginRedirectsToLogin()
        {
            $expectedStatus = 302;

            $this->client->request('GET', '/page/2');

            $this->assertResponseRedirects('/login');
            $this->assertEquals($expectedStatus, $this->client->getResponse()->getStatusCode());
        }

    /**
     * @test
     */
    public function accessToPageOneWithoutRolePrivileges()
    {

        $this->makeFakeLogin(1);

        $this->client->request('GET', '/page/1');

        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @test
     */
    public function accessToPageOneWithPageOneRolePrivileges()
    {

        $this->makeFakeLogin(2);

        $this->client->request('GET', '/page/1');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @test
     */
    public function accessToPageTwoWithoutRolePrivileges()
    {
        $this->makeFakeLogin(1);

        $this->client->request('GET', '/page/2');

        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @test
     */
    public function accessToPageTwoWithPageTwoRolePrivileges()
    {

        $this->makeFakeLogin(4);

        $this->client->request('GET', '/page/2');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @test
     */
    public function accessToPageOneWithAdminRolePrivileges()
    {

        $this->makeFakeLogin(3);

        $this->client->request('GET', '/page/1');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @test
     */
    public function accessToPageTwoWithAdminRolePrivileges()
    {

        $this->makeFakeLogin(3);

        $this->client->request('GET', '/page/2');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    private function addFakeUser(int $id): ?User
    {
       return $this->userRepository->find($id);
    }

    private function makeFakeLogin(int $idUser)
    {
        $session = self::$container->get('session');
        $user = $this->addFakeUser($idUser);
        $firewallName = 'main';
        $firewallContext = 'main';

        $this->createToken($user, $firewallName, $session, $firewallContext);

        $this->createCookie($session);
    }


    private function createToken(User $user, string $firewallName, Object $session, string $firewallContext)
    {
        $token = new UsernamePasswordToken($user, null, $firewallName, $user->getRoles());
        $session->set('_security_'.$firewallContext, serialize($token));
        $session->save();
    }

    private function createCookie(Object $session)
    {
        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

}