<?php

namespace App\Controller;

use App\Entity\Lieux;
use App\Form\LieuType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class LieuController extends AbstractController
{
    const ROUTE_LIEU_ADD = "app_lieu";

    #[Route('/lieu/add', name: self::ROUTE_LIEU_ADD)]
    public function add(EntityManagerInterface $entityManager,Request $request)
    {
        $lieu = new Lieux();
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
