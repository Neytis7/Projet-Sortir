<?php

namespace App\Controller;

use App\Form\RechercheSortieType;
use App\Entity\Sorties;
use App\Form\SortieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\SortiesRepository;

class SortieController extends AbstractController
{

    const ROUTE_SORTIE = "app_sortie";
    private EntityManagerInterface $em;

    #[Route('/sortie/add', name: 'sortie_add')]
    public function add(EntityManagerInterface $entityManager, Request $request)
    {
        // Creation de l'instance
        $sortie = new Sorties();
        //$sortie->setOrganisateur($this->getUser()->getUserIdentifier());

        // Creation d'un formulaire en fonction d'un wish
        $form = $this->createForm(SortieType::class,$sortie);
        // Recupere ce qui été envoyé
        $form->handleRequest($request);

        // Check si le formulaire est valide et envoyé
        if($form->isSubmitted() && $form->isValid()){

            $entityManager->persist($sortie);
            $entityManager->flush();
            // Message flash
            $this->addFlash('success','Sortie successfully added !');
            // Redirection vers la page des Sorties
            return $this->redirectToRoute('sortie_detail',['id'=>$sortie->getNoSortie()]);
        }
        // Affichage du formulaire
        $sortieForm = $form->createView();
        return $this->render('sortie/add.html.twig',compact('sortieForm'));
    }

    #[Route('/sortie', name: self::ROUTE_SORTIE)]
    public function index(Request $request, SortiesRepository $SortiesRepository): Response
    {
        $lesSorties=$SortiesRepository->findAll();

        $sorties = new Sorties();
        $form = $this->createForm(RechercheSortieType::class, $sorties);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
        }

        $formView = $form->createView();
        
        return $this->render('sortie/index.html.twig', [
            'lesSorties' => $lesSorties,
            'formView'=>$formView
        ]);
    }
}