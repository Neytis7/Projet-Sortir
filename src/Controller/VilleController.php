<?php

namespace App\Controller;

use App\Entity\Villes;
use App\Form\VilleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class VilleController extends AbstractController
{
    const ROUTE_VILLE_ADD = "app_ville";

    #[Route('/ville/add', name: self::ROUTE_VILLE_ADD)]
    public function add(EntityManagerInterface $entityManager,Request $request)
    {
        $ville = new Villes();
        $form = $this->createForm(VilleType::class,$ville);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($ville);
            $entityManager->flush();
            $this->addFlash('success','Ville successfully added !');
            return $this->redirectToRoute("app_lieu");
        }
        $villeForm = $form->createView();
        return $this->render('ville/add.html.twig',compact('villeForm'));
    }
}
