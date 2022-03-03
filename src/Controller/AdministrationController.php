<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Site;
use App\Form\ProfilType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AdministrationController extends AbstractController
{

    CONST ROUTE_ADMIN = "app_admin";

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function checkRole(){
        if(!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Vous n\'avez pas les droits nécessaires...');
            return $this->redirectToRoute(SortieController::ROUTE_SORTIE);
        }
    }

    #[Route('/admin', name: self::ROUTE_ADMIN)]
    public function index(?UserInterface $userCourant, Request $request, EntityManagerInterface $em , UserPasswordHasherInterface $pwdHasher ): Response
    {
        /** @var Participant $userCourant */
        if (is_null($userCourant)) {
            throw new AccessDeniedException('Veuillez vous connecter en tant qu\'admin pour accèder à la page !');
        }
        /* $this->checkRole(); */
        
        //Form CSV
        $defaultData = ['message' => 'Choisissez un fichier .csv valide pour l\'ajout d\'utilisateurs'];
        $formAddUserWithCsv = $this->createFormBuilder($defaultData)
            ->add('file', FileType::class)
            ->getForm()
        ;
        $formAddUserWithCsv->handleRequest($request);
        if ($formAddUserWithCsv->isSubmitted() && $formAddUserWithCsv->isValid()){
            $data = $formAddUserWithCsv->getData();
            $msg = "Ajout d'utilisateur :";
            //Lecture fichier
            $isFirst = true;
            if (($fp = fopen($data['file'], "r")) !== FALSE) 
            {
                while (($row = fgetcsv($fp, 1000, ";")) !== FALSE) 
                {
                    if($isFirst){
                        $isFirst = false;
                    } else {
                        $user = new Participant();
                        $user->setPseudo($row['0']);
                        $user->setNom($row['1']);
                        $user->setPrenom($row['2']);
                        $user->setTelephone($row['3']);
                        $user->setMail($row['4']);

                        $user->setMotDePasse(
                            $pwdHasher->hashPassword($user,$row['5'])
                        );

                        $user->setAdministrateur($row['6']);
                        $user->setActif($row['7']);

                        $site = $this->em->getRepository(Site::class)->find($row['8']);
                        $user->setSite($site);

                        $em->persist($user);
                        $msg.=" ".strval($user->getId());
                    }
                }
                fclose($fp);
            }
            $em->flush();
            $this->addFlash('success', $msg);
        }

        $sites = $this->em->getRepository(Site::class)->findAll();
        //Form add user
        $user = new Participant();
        $userFormBuilder = $this->createForm(ProfilType::class, $user, [
            'isAdmin' => true,
            'sites_choices' => $sites
        ]);

        $userFormBuilder->handleRequest($request);

        if($userFormBuilder->isSubmitted() && $userFormBuilder->isValid()) {
            $user->setMotDePasse(
                $pwdHasher->hashPassword(
                    $user,
                    $userFormBuilder->get('motDePasse')->getData()
                )
            );

            $em->persist($user);
            $em->flush();
            // ajout d'un message flash
            $userName = $user->getNom();
            $this->addFlash('success', "L'utilisateur $userName a été ajouté");
        }

        return $this->render('administration/admin.html.twig', [
            'controller_name' => 'AdministrationController',
            'addUserWithCsv_form' => $formAddUserWithCsv->createView(),
            'addUserManuel' => $userFormBuilder->createView()
        ]);
    }
}
