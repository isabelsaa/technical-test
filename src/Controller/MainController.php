<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class MainController extends AbstractController
{
    public function first(UserInterface $user): Response
    {
        return $this->render('User/firstWelcome.html.twig',
            ['username' => $user->getUsername()]);
    }
    
    public function second(UserInterface $user): Response
    {
        return $this->render('User/secondWelcome.html.twig',
            ['username' => $user->getUsername()]);
    }
}
