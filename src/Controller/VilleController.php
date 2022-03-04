<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Require ROLE_ADMIN for all the actions of this controller
 *
 * @IsGranted("ROLE_USER")
 */
class VilleController extends AbstractController
{
    const ROUTE_VILLE_ADD = "app_ville";

    #[Route('/ville/add', name: self::ROUTE_VILLE_ADD)]
    public function add(EntityManagerInterface $entityManager,Request $request)
    {

        $ville = new Ville();
        $form = $this->createForm(VilleType::class, $ville);
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
