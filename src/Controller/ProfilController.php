<?php

namespace App\Controller;

use App\Repository\ParticipantsRepository;
use App\Entity\Participants;
use App\Form\ProfilType;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\ProfilService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
    const PATH_HOME = 'app_main';
    const ROUTE_MODIFIER_PROFIL = 'modifier_profil';
    const ROUTE_NAME_MODIFIER_PROFIL = 'Profil';

    /**
     * @var ProfilService
     */
    private ProfilService $profilService;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * @param ProfilService $profilService
     * @param EntityManagerInterface $em
     */
    public function __construct(
        ProfilService $profilService,
        EntityManagerInterface $em
    ) {
        $this->profilService = $profilService;
        $this->em = $em;
    }

    /**
     */
    #[Route('modifier/mon-profil', name: 'modifier_profil')]
    public function profil(Request $request): Response
    {
        $redirectRoute = self::PATH_HOME;
        $userCourant = $this->getUser();
        $form = $this->createForm(ProfilType::class, $userCourant);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            if ($form->isValid()) {
                $newPseudo = $form->get('pseudo')->getData();
                $estUnique = $this->profilService->estPseudoUnique(
                    $newPseudo
                );

                if (!$estUnique) {
                    $this->addFlash('error', 'Ce pseudo est déjà utilisé !');
                    $redirectRoute = self::ROUTE_MODIFIER_PROFIL;
                } else {
                    /** @var Participants $userCourant */
                    //TODO theophane : verifier que cela fonctionne lorsque la classe user sera implémenté
                    $this->em->persist($userCourant);
                    $this->em->flush();

                    $this->addFlash('success', 'Votre profil à été mis à jour');
                }
            } else {
                $redirectRoute = self::ROUTE_MODIFIER_PROFIL;
            }
            return $this->redirectToRoute($redirectRoute);
        }

        return $this->render('profil/profil.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('afficher/profil/{id}', name: 'afficherProfile')]
    public function voirProfil(Request $request, ParticipantsRepository $participantsRepository, int $id): Response
    {
        $leParticipant=$participantsRepository->find($id);
        return $this->render('profil/afficherProfil.html.twig', [
            'leParticipant' => $leParticipant
        ]);
    }

    /**
     * @Route("/", name="accueil")
     */
    public function index(): Response
    {
        return $this->render('base.html.twig');
    }
    
}
