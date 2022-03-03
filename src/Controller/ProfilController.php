<?php

namespace App\Controller;

use App\Entity\Site;
use App\Repository\ParticipantRepository;
use App\Entity\Participant;
use App\Form\ProfilType;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\ProfilService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class ProfilController extends AbstractController
{
    const PATH_HOME = 'app_main';
    const ROUTE_MODIFIER_PROFIL = 'modifier_profil';
    const ROUTE_NAME_MODIFIER_PROFIL = 'Profil';
    const ROUTE_AFFICHER_PROFIL = "afficherProfile";

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * @var UserPasswordHasherInterface
     */
    private UserPasswordHasherInterface $passwordEncoder;

    /**
     * @param EntityManagerInterface $em
     * @param UserPasswordHasherInterface $passwordEncoder
     */
    public function __construct(
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordEncoder
    ) {
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     */
    #[Route('modifier/mon-profil', name: 'modifier_profil')]
    public function profil(?UserInterface $userCourant, Request $request): Response
    {
        /** @var Participant $userCourant */
        if (is_null($userCourant)) {
            throw new AccessDeniedException('Veuillez vous connecter pour accèder à cette page.');
        }

        $isAdmin = false;
        $isActif = false;
        $redirectRoute = SortieController::ROUTE_SORTIE;
        $utilisateurBdd = $this->em->getRepository(Participant::class)->find($userCourant->getId());

        $sites = $this->em->getRepository(Site::class)->findAll();

        if (!is_null($utilisateurBdd)) {
            $isAdmin = $utilisateurBdd->isAdministrateur();
            $isActif = $utilisateurBdd->isActif();
        }

        $form = $this->createForm(ProfilType::class, $userCourant, [
            'isAdmin' => $isAdmin,
            'sites_choices' => $sites,
            'participant' => $userCourant
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            if ($form->isValid()) {
                $nouveauMdpHash = $this->passwordEncoder->hashPassword(
                    $userCourant,
                    $form->get('motDePasse')->getData()
                );

                if (!is_null($nouveauMdpHash)) {
                    $userCourant->setMotDePasse(
                        $this->passwordEncoder->hashPassword(
                            $userCourant,
                            $form->get('motDePasse')->getData()
                        )
                    );
                }

                if (!$isAdmin) {
                    $utilisateurBdd->setAdministrateur(false);
                    $utilisateurBdd->setActif($isActif);
                }

                /** @var Participant $userCourant */
                $this->em->flush();
                $this->addFlash('success', 'Votre profil à été mis à jour');
                return $this->redirectToRoute($redirectRoute);
                
            } else {
                $this->em->refresh($userCourant);
                $this->addFlash('error', 'Impossible de modifier le profil, veuillez vérifier les données saisies !');
            }
        }

        return $this->render('profil/profil.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('afficher/profil/{id}', name: self::ROUTE_AFFICHER_PROFIL)]
    public function voirProfil(Request $request, int $id): Response
    {
        $leParticipant = $this->em->getRepository(Participant::class)->find($id);

        $form = $this->createForm(ProfilType::class, $leParticipant, [
            'isAdmin' => false,
            'disabled' => true,
            'participant' => $leParticipant
        ]);

        $form->handleRequest($request);

        return $this->render('profil/afficherProfil.html.twig', [
            'form' => $form->createView(),
            'leParticipant' => $leParticipant
        ]);
    }
}
