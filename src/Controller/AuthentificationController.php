<?php

namespace App\Controller;
use App\Entity\Participant;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthentificationController extends AbstractController
{
    const ROUTE_LOGOUT= "logout";
    private AuthenticationUtils $authenticationUtils;
    private RequestStack $requestStack;


    public function __construct(AuthenticationUtils $authenticationUtils, RequestStack $requestStack)
    {
       $this->authenticationUtils = $authenticationUtils;
        $this->requestStack = $requestStack;

    }

    #[Route('/', name: 'login')]
    public function login(): Response
    {
        // get the login error if there is one
        $error = $this->authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $this->authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    #[Route('/logout',name: self::ROUTE_LOGOUT)]
    public function logout(): void
    {
        $session = $this->requestStack->getSession();
        $session->invalidate();
        $session->clear();
    }
}
