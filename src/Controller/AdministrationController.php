<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class AdministrationController extends AbstractController
{

    CONST ROUTE_ADMIN = "app_admin";

    public function checkRole(){
        if(!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Vous n\'avez pas les droits nécessaires...');
            return $this->redirectToRoute(MainController::ROUTE_MAIN);
        }
    }

    #[Route('/admin', name: self::ROUTE_ADMIN)]
    public function index(Request $request): Response
    {
        /* $this->checkRole(); */
        
        $defaultData = ['message' => 'Choisissez un fichier .csv valide pour l\'ajout d\'utilisateurs'];
        $formAddUserWithCsv = $this->createFormBuilder($defaultData)
            ->add('file', FileType::class)
            ->getForm()
        ;

        $formAddUserWithCsv->handleRequest($request);

        if ($formAddUserWithCsv->isSubmitted() && $formAddUserWithCsv->isValid()){
            $data = $formAddUserWithCsv->getData();
            dd($data);
        }

        return $this->render('administration/index.html.twig', [
            'controller_name' => 'AdministrationController',
            'addUserWithCsv_form' => $formAddUserWithCsv->createView()
        ]);
    }
}
