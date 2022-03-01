<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SortiesRepository;

class SortieController extends AbstractController
{
    private EntityManagerInterface $em;

    function __construct(EntityManagerInterface $em){
     $this->em=$em;
    }

    #[Route('/sortie', name: 'app_sortie')]
    public function index(SortiesRepository $SortiesRepository): Response
    {
        $lesSorties=$SortiesRepository->findAll();
        
        return $this->render('sortie/index.html.twig', [
            'lesSorties' => $lesSorties,
        ]);
    }
}
