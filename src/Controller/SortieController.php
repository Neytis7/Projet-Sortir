<?php

namespace App\Controller;

use App\Form\RechercheSortieType;
use App\Entity\Sorties;
use App\Form\SortieAnnuleType;
use App\Form\SortieType;
use App\Repository\EtatsRepository;
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
    public function add(EntityManagerInterface $entityManager,EtatsRepository $etatsRepository, Request $request)
    {
        // Creation de l'instance
        $etatsCreer = $etatsRepository->findCree();
        $sortie = new Sorties();
        //$sortie->setOrganisateur($this->getUser()->getUserIdentifier());

        // Creation d'un formulaire en fonction d'un wish
        $form = $this->createForm(SortieType::class,$sortie);
        // Recupere ce qui été envoyé
        $form->handleRequest($request);

        // Check si le formulaire est valide et envoyé
        if($form->isSubmitted() && $form->isValid()){
            $sortie->setEtatsNoEtat($etatsCreer[0]);
            $entityManager->persist($sortie);
            $entityManager->flush();
            // Message flash
            $this->addFlash('success','Sortie successfully added !');
            // Redirection vers la page des Sorties
            return $this->redirectToRoute(self::ROUTE_SORTIE);
        }
        // Affichage du formulaire
        $sortieForm = $form->createView();
        return $this->render('sortie/add.html.twig',compact('sortieForm'));
    }

    #[Route('/sortie/{id}', name: 'sortie_detail',requirements: ['id'=>'\d+'])]
    public function detail($id,SortiesRepository $SortiesRepository): Response
    {
        $sortie = $SortiesRepository->find($id);
        if(!$sortie){
            throw new NotFoundHttpException("This wish doesn't exist");
        }
        return $this->render('sortie/detail.html.twig',
            compact("id",'sortie'));
    }

    #[Route('/sortie/modified/{id}', name: 'sortie_modified',requirements: ['id'=>'\d+'])]
    public function modified($id,SortiesRepository $SortiesRepository,Request $request,EntityManagerInterface $entityManager): Response
    {
        $sortie = $SortiesRepository->find($id);

        if(!$sortie){
            throw new NotFoundHttpException("This sortie doesn't exist");
        }else {
            // Creation d'un formulaire en fonction d'un wish
            $form = $this->createForm(SortieType::class, $sortie);

            // Recupere ce qui été envoyé
            $form->handleRequest($request);

            // Check si le formulaire est valide et envoyé
            if ($form->isSubmitted() && $form->isValid()) {

                $entityManager->persist($sortie);
                $entityManager->flush();
                // Message flash
                $this->addFlash('success', 'Sortie successfully modified !');
                // Redirection vers la page des Sorties
                return $this->redirectToRoute(self::ROUTE_SORTIE);
            }
            // Affichage du formulaire
            $sortieForm = $form->createView();
        }
        return $this->render('sortie/modified.html.twig',
            compact("id",'sortie','sortieForm'));
    }

    #[Route('/sortie/annule/{id}', name: 'sortie_annule',requirements: ['id'=>'\d+'])]
    public function annule($id,SortiesRepository $SortiesRepository,EtatsRepository $etatsRepository,Request $request,EntityManagerInterface $entityManager)
    {
        $sortie = $SortiesRepository->find($id);
        $etatsAnnule = $etatsRepository->findAnnulee();

        if(!$sortie){
            throw new NotFoundHttpException("This sortie doesn't exist");
        }else {
            $form = $this->createForm(SortieAnnuleType::class,$sortie);
            $form->handleRequest($request);

            // Check si le formulaire est valide et envoyé
            if ($form->isSubmitted() && $form->isValid()) {

                $sortie->setEtatsNoEtat($etatsAnnule[0]);
                $entityManager->persist($sortie);
                $entityManager->flush();
                // Message flash
                $this->addFlash('success', 'Sortie successfully annulé !');
                // Redirection vers la page des Sorties
                return $this->redirectToRoute(self::ROUTE_SORTIE);
            }
            // Affichage du formulaire
            $sortieForm = $form->createView();
        }
        return $this->render('sortie/annule.html.twig',
            compact("id",'sortie','sortieForm'));
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