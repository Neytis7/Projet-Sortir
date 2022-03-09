<?php

namespace App\Controller;

use App\Entity\Site;
use App\Entity\Participant;
use App\Form\ProfilType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


/**
 * Require ROLE_ADMIN for all the actions of this controller
 *
 * @IsGranted("ROLE_USER")
 */
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
    private SluggerInterface $slugger;

    /**
     * @param EntityManagerInterface $em
     * @param UserPasswordHasherInterface $passwordEncoder
     * @param SluggerInterface $slugger
     */
    public function __construct(
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordEncoder,
        SluggerInterface $slugger
    ) {
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
        $this->slugger = $slugger;
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
            $isAdmin = $utilisateurBdd->hasRole('ROLE_ADMIN');
            $isActif = $utilisateurBdd->isActif();
        }

        $form = $this->createForm(ProfilType::class, $userCourant, [
            'isAdmin' => $isAdmin,
            'sites_choices' => $sites,
            'participant' => $userCourant
        ]);

        dump('dump');
        $form->handleRequest($request);
        //dd('stop');
        if ($form->isSubmitted()) {

            if ($form->isValid()) {
                dd('lalalal');
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

                $imageProfil = $form->get('image')->getData();

                if($imageProfil){
                    $OriFilename = pathinfo($imageProfil->getClientOriginalName(), PATHINFO_FILENAME);

                    $safeFilename = $this->slugger->slug($OriFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$imageProfil->guessExtension();
                    try {
                        $imageProfil->move(
                            $this->getParameter('profile_directory'),
                            $newFilename
                        );

                    }catch (FileException $e){}

                    $utilisateurBdd->setPhoto($newFilename);
                }

                $utilisateurBdd->setActif($isActif);

                if (!$isAdmin) {
                    $utilisateurBdd->setRoles(array('ROLE_USER'));
                } else {
                    $utilisateurBdd->setRoles(array('ROLE_ADMIN'));
                }

                dd('laaaaa');
                $this->em->flush();
                $this->addFlash('success', 'Votre profil à été mis à jour');
                return $this->redirectToRoute($redirectRoute);
                
            } else {
                dd('ici');
                $this->em->refresh($utilisateurBdd);
                $this->addFlash('error', 'Impossible de modifier le profil, veuillez vérifier les données saisies !');
            }
        }

        return $this->render('profil/profil.html.twig', [
            'form' => $form->createView(),
            'userCourant' => $userCourant
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
