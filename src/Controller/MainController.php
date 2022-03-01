<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{

    const ROUTE_MAIN = "app_main";
    const ROUTE_MAIN2 = "app_main2";

    #[Route('/main', name: self::ROUTE_MAIN)]
    public function index(): Response
    {
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController'
        ]);
    }

    #[Route('/main2', name: self::ROUTE_MAIN2)]
    public function index2(): Response
    {
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController'
        ]);
    }
}
