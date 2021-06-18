<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserApiController extends AbstractController
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index(): Response
    {
        return new Response(
            '<p>Mi API</p>'
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addUser(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (empty($data)) {
            throw new HttpException(204, "You must add this parameters");
        }

        $name = $data['name'];
        $userName = $data['username'];
        $password = $data['password'];
        $roles = $data['roles'];

        $this->userRepository->saveUser($name, $userName, $password, $roles);

        return new JsonResponse(['status' => 'New User created'], Response::HTTP_CREATED);
    }

    /**
     * @param $name
     * @return JsonResponse
     */
    public function removeUser($name): JsonResponse
    {
        $user = $this->userRepository->findOneBy(['name' => $name]);
        $this->userRepository->deleteUser($user);

        return new JsonResponse(['status' => 'User deleted'], Response::HTTP_OK);
    }


    /**
     * @param Request $request
     * @param $name
     * @return JsonResponse
     */
    public function updateUser(Request $request, $name): JsonResponse
    {
        $user = $this->userRepository->findOneBy(['name' => $name]);
        $data = json_decode($request->getContent(), true);
        empty($data['name']) ? true : $user->setName($data['name']);
        empty($data['username']) ? true : $user->setName($data['username']);
        empty($data['password']) ? true : $user->setName($data['password']);
        empty($data['roles']) ? true : $user->setName($data['roles']);

        try {
            $this->userRepository->updateUser($user);
        } catch (OptimisticLockException | ORMException $e) {
        }

        return new JsonResponse(['status' => 'User updated'], Response::HTTP_OK);
    }
}
