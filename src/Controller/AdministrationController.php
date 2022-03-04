<?php

namespace App\Controller;

use App\Repository\ParticipantRepository;
use App\Entity\Participant;
use App\Entity\Site;
use App\Form\ProfilType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * Require ROLE_ADMIN for all the actions of this controller
 *
 * @IsGranted("ROLE_ADMIN")
 */
class AdministrationController extends AbstractController
{

    CONST ROUTE_ADMIN = "app_admin";
    CONST ROUTE_ADMIN_USER_ACTIF = "app_admin_changeUserActif";
    CONST ROUTE_ADMIN_USER_DELETE = "app_admin_DeleteUser";

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

    public function index(?UserInterface $userCourant, Request $request, UserPasswordHasherInterface $pwdHasher, ParticipantRepository $participantsRepository ): Response
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
            $userMsg = "Ajout d'utilisateur :";
            $siteMsg = "";
            $errMsg = "";
            $line = 0;
            //Lecture fichier
            $isFirst = true;
            if (($fp = fopen($data['file'], "r")) !== FALSE) 
            {
                while (($row = fgetcsv($fp, 1000, ",")) !== FALSE) 
                {
                    $line++;
                    if($isFirst){
                        $isFirst = false;
                    } else {
                        try{
                            //dd($row);
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

                            $site = $this->em->getRepository(Site::class)->findOneBy(['nom' => $row['8']]);

                            if($site == null){
                                $site = new Site();
                                $site->setNom($row['8']);
                                $this->em->persist($site);
                                $this->em->flush();
                                $siteMsg.=" ".strval($site->getId());
                            }
                            $user->setSite($site);

                            $this->em->persist($user);
                            $this->em->flush();
                            $userMsg.=" ".strval($user->getId());
                        }
                        catch(\Exception $e){
                            
                            $errMsg .= " erreur d'insertion sur la ligne ".strval($line);
                            //error_log($e->getMessage());
                        }
                        
                    }
                }
                fclose($fp);
            }
            
            $this->addFlash('success', $userMsg);
            if($errMsg !== null && $errMsg !== ""){
                $this->addFlash('error', $errMsg);
            }
            if($siteMsg !== null && $siteMsg !== ""){
                $this->addFlash('success', "Ajout de site :".$siteMsg);
            }
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

            $this->em->persist($user);
            $this->em->flush();
            // ajout d'un message flash
            $userName = $user->getNom();
            $this->addFlash('success', "L'utilisateur $userName a été ajouté");
        }

        //Recuperation de tout les utilisateurs
        $allUsers = $participantsRepository->findAll();

        return $this->render('administration/admin.html.twig', [
            'controller_name' => 'AdministrationController',
            'addUserWithCsv_form' => $formAddUserWithCsv->createView(),
            'addUserManuel' => $userFormBuilder->createView(),
            'allUsers' => $allUsers
        ]);
    }

    #[Route('/admin/changeActif/{id}', name: self::ROUTE_ADMIN_USER_ACTIF)]
    public function changeActifUser(int $id = 0): Response
    {
        /* $this->checkRole(); */

        if($id != 0){
            $user = $this->em->getRepository(Participant::class)->find($id);
            $user->setActif(!$user->getActif());
            $this->em->persist($user);
            $this->em->flush();

            $msgStatus = "inactif";
            if($user->getActif()){
                $msgStatus = "actif";
            }

            //dd($user);
            $this->addFlash('success', "L'utilisateur ".$user->getId()." est désormais ".$msgStatus);
        }
        
        return $this->redirectToRoute(self::ROUTE_ADMIN);
    }

    #[Route('/admin/deleteUser/{id}', name: self::ROUTE_ADMIN_USER_DELETE)]
    public function deleteUser(int $id = 0): Response
    {
        /* $this->checkRole(); */

        if($id != 0){
            $user = $this->em->getRepository(Participant::class)->find($id);
            $this->em->remove($user);
            $this->em->flush();

            $this->addFlash('success', "L'utilisateur ".$id." a été supprimé ");
        }
        
        return $this->redirectToRoute(self::ROUTE_ADMIN);
    }
}
