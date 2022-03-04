<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Require ROLE_ADMIN for all the actions of this controller
 *
 * @IsGranted("ROLE_USER")
 */
class LieuController extends AbstractController
{
    const ROUTE_LIEU_ADD = "app_lieu";

    #[Route('/lieu/add', name: LieuController::ROUTE_LIEU_ADD)]
    public function add(EntityManagerInterface $entityManager,Request $request): RedirectResponse|Response
    {


        $lieu = new Lieu();
        $form = $this->createForm(LieuType::class,$lieu);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($lieu);
            $entityManager->flush();
            $this->addFlash('success','Lieu successfully added !');
            return $this->redirectToRoute("sortie_add");
        }
        $lieuForm = $form->createView();
        return $this->render('lieu/add.html.twig',compact('lieuForm'));
    }
}
