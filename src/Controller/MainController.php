<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/page/1")
     */
    public function action(): Response
    {

        return $this->render('User/firstWelcome.html.twig');
    }

    /**
     * @Route("/page/2")
     */
    public function second(): Response
    {

        return $this->render('User/secondWelcome.html.twig');
    }
}