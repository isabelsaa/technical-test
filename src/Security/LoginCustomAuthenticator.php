<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginCustomAuthenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';
    private EntityManagerInterface $entityManager;
    private UrlGeneratorInterface $urlGenerator;
    private CsrfTokenManagerInterface $csrfTokenManager;


    public function __construct(
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator,
        CsrfTokenManagerInterface $csrfTokenManager
    ) {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    public function supports(Request $request): bool
    {
        return self::LOGIN_ROUTE === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    #[ArrayShape(['username' => "mixed", 'password' => "mixed", 'csrf_token' => "mixed"])]
    public function getCredentials(Request $request): array
    {
        $credentials = [
            'username' => $request->request->get('username'),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['username']
        );

        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider): object|null
    {
        $user = $this->entityManager->getRepository(User::class)
            ->findOneBy(['username' => $credentials['username']]);

        if (!$user) {
            throw new UsernameNotFoundException('Username could not be found.');
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        if ($user->getPassword() === $credentials['password']) {
            return true;
        }
        return false;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): RedirectResponse
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }
        if ($token->getUser()->getRoles() === ['ROLE_PAGE_1']) {
            return new RedirectResponse($this->urlGenerator->generate('index'));
        }

        if ($token->getUser()->getRoles() === ['ROLE_PAGE_2']) {
            return new RedirectResponse($this->urlGenerator->generate('second'));
        }

        return new RedirectResponse($this->urlGenerator->generate('index'));
    }

    protected function getLoginUrl(): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
