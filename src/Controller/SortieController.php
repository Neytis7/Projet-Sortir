<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Participant;
use App\Form\RechercheSortieType;
use App\Entity\Sortie;
use App\Form\SortieAnnuleType;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Service\SortieService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\SortieRepository;
use Symfony\Component\Security\Core\User\UserInterface;

class SortieController extends AbstractController
{

    const ROUTE_SORTIE = "app_sortie";
    const ROUTE_CREER_SORTIE = "sortie_add";
    const ROUTE_MODIFIED_SORTIE = "sortie_modified";
    const ROUTE_ANNULE_SORTIE = "sortie_annule";
    const ROUTE_DETAIL_SORTIE = "sortie_detail";
    const ROUTE_INSCRIPTION_SORTIE = "inscription_sortie";

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * @var SortieService
     */
    private SortieService $serviceSortie;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(
        EntityManagerInterface $em,
        SortieService $serviceSortie
    ) {
        $this->em = $em;
        $this->serviceSortie = $serviceSortie;
    }

    #[Route('/sortie/add', name: self::ROUTE_CREER_SORTIE)]
    public function add(EntityManagerInterface $entityManager, EtatRepository $etatsRepository, Request $request)
    {
        // Creation de l'instance
        $sortie = new Sortie();
        $sortie->setOrganisateur($this->getUser());

        // Creation d'un formulaire en fonction d'une sortie
        $form = $this->createForm(SortieType::class,$sortie,['dateJour'=> (new \DateTime())->format('d/m/Y h:i:s')]);
        // Recupere ce qui été envoyé
        $form->handleRequest($request);

        // Check si le formulaire est valide et envoyé
        if($form->isSubmitted() && $form->isValid()){

            $etat = $etatsRepository->findOneBy([
                'libelle' => 'Créée'
            ]);

            $sortie->setEtat($etat);
            $entityManager->persist($sortie);
            $entityManager->flush();
            // Message flash
            $this->addFlash('success','Sortie successfully added !');
            // Redirection vers la page des Sortie
            return $this->redirectToRoute(self::ROUTE_SORTIE);
        }
        // Affichage du formulaire
        $sortieForm = $form->createView();
        return $this->render('sortie/add.html.twig',compact('sortieForm'));
    }

    #[Route('/sortie/{id}', name: self::ROUTE_DETAIL_SORTIE,requirements: ['id'=>'\d+'])]
    public function detail($id, SortieRepository $SortiesRepository): Response
    {
        $sortie = $SortiesRepository->find($id);
        if(!$sortie){
            throw new NotFoundHttpException("This sortie doesn't exist");
        }
        return $this->render('sortie/detail.html.twig',
            compact("id",'sortie'));
    }

    #[Route('/sortie/modified/{id}', name: self::ROUTE_MODIFIED_SORTIE,requirements: ['id'=>'\d+'])]
    public function modified($id, SortieRepository $SortiesRepository, Request $request, EntityManagerInterface $entityManager): Response
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
                // Redirection vers la page des Sortie
                return $this->redirectToRoute(self::ROUTE_SORTIE);
            }
            // Affichage du formulaire
            $sortieForm = $form->createView();
        }
        return $this->render('sortie/modified.html.twig',
            compact("id",'sortie','sortieForm'));
    }

    #[Route('/sortie/annule/{id}', name: self::ROUTE_ANNULE_SORTIE,requirements: ['id'=>'\d+'])]
    public function annule(
        $id,
        SortieRepository $SortiesRepository,
        EtatRepository $etatsRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ) {
        $sortie = $SortiesRepository->find($id);
        $etatsAnnule = $etatsRepository->findOneBy([
            'libelle' => 'Annulée'
        ]);

        if(!$sortie){
            throw new NotFoundHttpException("This sortie doesn't exist");
        }else {
            $form = $this->createForm(SortieAnnuleType::class,$sortie);
            $form->handleRequest($request);

            // Check si le formulaire est valide et envoyé
            if ($form->isSubmitted() && $form->isValid()) {

                $etat = $this->em->getRepository(Etat::class)->find($etatsAnnule[0]);

                $sortie->setEtat($etat);
                $entityManager->persist($sortie);
                $entityManager->flush();

                // Message flash
                $this->addFlash('success', 'Sortie successfully annulé !');
                // Redirection vers la page des Sortie
                return $this->redirectToRoute(self::ROUTE_SORTIE);
            }
            // Affichage du formulaire
            $sortieForm = $form->createView();
        }
        return $this->render('sortie/annule.html.twig',
            compact("id",'sortie','sortieForm'));
    }

    #[Route('/sortie', name: self::ROUTE_SORTIE)]
    public function index(Request $request, SortieRepository $SortiesRepository): Response
    {
        $lesSorties = $SortiesRepository->findAll();

        $sorties = new Sortie();
        $form = $this->createForm(RechercheSortieType::class, $sorties);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
        }

        $formView = $form->createView();
        
        return $this->render('sortie/index.html.twig', [
            'lesSorties' => $lesSorties,
            'formView'=> $formView
        ]);
    }

    #[Route('/inscription/sortie/{id}', name: self::ROUTE_INSCRIPTION_SORTIE, requirements: ['id'=>'\d+'])]
    public function inscriptionSortie(?UserInterface $userCourant, Request $request): Response
    {
        /** @var Participant $userCourant */
        if (is_null($userCourant)) {
            throw new AccessDeniedException('Veuillez vous connecter pour vous inscrire à une activité !');
        }

        $sortie = $this->em->getRepository(Sortie::class)->find($request->get('id'));

        if (is_null($sortie)) {
            throw new AccessDeniedException('La sortie n\'a pas été trouvé, veuillez réessayer');
        }

        $inscriptionSuccess = $this->serviceSortie->inscrireSortie($userCourant, $sortie);

        $message = $inscriptionSuccess === true
            ? "Votre inscription a été prise en compte !"
            : "L'inscription à la sortie à échouée !";

        $type = $inscriptionSuccess === true
            ? "success"
            : "error";

        $this->addFlash($type, $message);

        return $this->redirectToRoute(self::ROUTE_SORTIE);
    }
}