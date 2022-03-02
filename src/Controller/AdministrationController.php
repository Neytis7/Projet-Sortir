<?php

namespace App\Controller;

use App\Entity\Participants;
use App\Form\ProfilType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdministrationController extends AbstractController
{

    CONST ROUTE_ADMIN = "app_admin";

    public function checkRole(){
        if(!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Vous n\'avez pas les droits nécessaires...');
            return $this->redirectToRoute(SortieController::ROUTE_SORTIE);
        }
    }

    #[Route('/admin', name: self::ROUTE_ADMIN)]
    public function index(Request $request, EntityManagerInterface $em , UserPasswordHasherInterface $pwdHasher ): Response
    {
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
                        $user = new Participants();
                        $user->setPseudo($row['0']);
                        $user->setNom($row['1']);
                        $user->setPrenom($row['2']);
                        $user->setTelephone($row['3']);
                        $user->setMail($row['4']);

                        $user->setMotDePasse(
                            $pwdHasher->hashPassword($user,$row['5'])
                        );

                        //$user->setMotDePasse($row['5']);
                        $user->setAdministrateur($row['6']);
                        $user->setActif($row['7']);
                        $user->setSitesNoSite($row['8']); 

                        //dump($user);
                        $em->persist($user);
                        $em->flush();
                        $msg.=" ".strval($user->getNoParticipant());
                    }
                }
                fclose($fp);
            }
            $this->addFlash('success', $msg);
        }

        //Form add user
        $user = new Participants();
        $userFormBuilder = $this->createForm(ProfilType::class, $user, [
            'isAdmin' => true,
        ]);

        $userFormBuilder->handleRequest($request);
        if($userFormBuilder->isSubmitted() && $userFormBuilder->isValid()) {
            
            $user->setMotDePasse(
                $pwdHasher->hashPassword($user,$userFormBuilder->get('motDePasse')->getData())
            );

            $em->persist($user);
            $em->flush();
            // ajout d'un message flash
            $userName = $user->getName();
            $this->addFlash('success', "L'utilisateur $userName a été ajouté");
        }

        return $this->render('administration/admin.html.twig', [
            'controller_name' => 'AdministrationController',
            'addUserWithCsv_form' => $formAddUserWithCsv->createView(),
            'addUserManuel' => $userFormBuilder->createView()
        ]);
    }
}
