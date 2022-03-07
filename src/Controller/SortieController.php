<?php

namespace App\Controller;



use App\Entity\Etat;
use App\Entity\Participant;
use App\Form\RechercheSortieType;
use App\Entity\Sortie;
use App\Form\SortieAnnuleType;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Service\SortieService;
use DateTime;
use DateTimeZone;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Joli\JoliNotif\Notification;
use Joli\JoliNotif\Notifier\NullNotifier;
use Joli\JoliNotif\NotifierFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Cookie;

use Symfony\Component\Mercure\Hub;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Repository\SortieRepository;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;


/**
 * Require ROLE_ADMIN for all the actions of this controller
 *
 * @IsGranted("ROLE_USER")
 */
class SortieController extends AbstractController
{

    const ROUTE_SORTIE = "app_sortie";
    const ROUTE_CREER_SORTIE = "sortie_add";
    const ROUTE_MODIFIED_SORTIE = "sortie_modified";
    const ROUTE_ANNULE_SORTIE = "sortie_annule";
    const ROUTE_DETAIL_SORTIE = "sortie_detail";
    const ROUTE_INSCRIPTION_SORTIE = "inscription_sortie";
    const ROUTE_DESINSCRIPTION_SORTIE = "desinscription_sortie";
    const ROUTE_SORTIE_RECHERCHER = "sortieRechercher";
    const ROUTE_SORTIE_INVITE = "sortieInvite";


    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * @var SortieService
     */
    private SortieService $serviceSortie;
    private $entityManager;
    private Sortie $sortie;

    private SluggerInterface $slugger;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(
        EntityManagerInterface $em,
        SortieService $serviceSortie,
        SluggerInterface $slugger
    ) {
        $this->em = $em;
        $this->serviceSortie = $serviceSortie;
        $this->slugger = $slugger;
    }

    #[Route('/sortie/add', name: self::ROUTE_CREER_SORTIE)]
    public function add(EntityManagerInterface $entityManager, EtatRepository $etatsRepository, Request $request)
    {

        // Creation de l'instance
        $sortie = new Sortie();
        /** @var Participant $participant */
        $participant = $this->getUser();
        $sortie->setOrganisateur($participant);

        // Creation d'un formulaire en fonction d'une sortie
        $form = $this->createForm(SortieType::class, $sortie);

        // Recupere ce qui été envoyé
        $form->handleRequest($request);

        // Check si le formulaire est valide et envoyé
        if($form->isSubmitted() && $form->isValid()){
            
            $etat = $etatsRepository->findOneBy([
                'libelle' => 'Créée'
            ]);


            $sortie->setPrive(boolval($form->getData('prive')));
            $this->extracted($form, $sortie);

            $sortie->setEtat($etat);
            $participant->addSortie($sortie);
            $entityManager->persist($sortie);
            $entityManager->flush();
            // Message flash
            $this->addFlash('success','Sortie successfully added !');
            // Redirection vers la page des Sortie
            return $this->redirectToRoute(self::ROUTE_SORTIE);
        }
        // Affichage du formulaire
        $sortieForm = $form->createView();
        return $this->render('sortie/add.html.twig',compact('sortieForm'));
    }


    /**
     * @param MailerInterface $mailer
     * @param SortieRepository $SortiesRepository
     * @param UserInterface|null $userCourant
     * @return RedirectResponse
     * @throws TransportExceptionInterface
     */
    #[Route('/sortie/invite/{id}', name: self::ROUTE_SORTIE_INVITE)]
    public function invite($id, MailerInterface $mailer,Sortie $sortie, SortieRepository $SortiesRepository,?UserInterface $userCourant): Response
    {
        $participant = $this->em->getRepository(Participant::class)->find($id);
        /** @var Participant $userCourant */
        $this->sortie = $sortie;
        $idSortie = $sortie->getId();
        if ($participant !== null){

            $email = (new TemplatedEmail())
                ->from(Address::create('Bobby de Sortie.com <projet.sortieeninantes@gmail.com>'))
                ->to($participant->getMail())
                ->subject('Vous avez reçu une invitation à une sortie ')
                ->htmlTemplate('sortie/mail.html.twig')
                ->context([
                    'idSortie' => $idSortie,
                ]);

            $mailer->send($email);

            $notifier = NotifierFactory::create();

            if (!($notifier instanceof NullNotifier)) {
                $notification =
                    (new Notification())
                        ->setTitle('Invitation envoyée')
                        ->setBody('L\'invitation à correctement été envoyée par mail à '.$participant->getPseudo())
                        ->setIcon(__DIR__.'/icon-success.png')
                ;
                $result = $notifier->send($notification);

                echo 'Notification ', $result ? 'successfully sent' : 'failed', ' with ', get_class($notifier), \PHP_EOL;
            } else {
                echo 'No supported notifier', \PHP_EOL;}


        }


            return $this->redirectToRoute('sortie_detail',array('id' => $idSortie));
    }

    #[Route('/sortie/{id}', name: self::ROUTE_DETAIL_SORTIE,requirements: ['id'=>'\d+'])]
    public function detail($id, SortieRepository $SortiesRepository,?UserInterface $userCourant): Response
    {
        /** @var Participant $userCourant */
        $sortie = $SortiesRepository->find($id);
        $idUserCourant = $userCourant->getId();

        $rsm = new ResultSetMapping();
        $rsm->addEntityResult(Participant::class,'participant');
        $rsm->addFieldResult('participant', 'id', 'id');

        $rsm->addFieldResult('participant', 'pseudo', 'pseudo');
        $rsm->addFieldResult('participant', 'nom', 'nom');
        $rsm->addFieldResult('participant', 'prenom', 'prenom');


        $query = $this->em->createNativeQuery('SELECT participant.id, pseudo,nom,prenom FROM participant WHERE id NOT IN (SELECT participant_id FROM participant_sortie
                                           left join participant on participant.id = participant_id
                                           where sortie_id = ?)
', $rsm);

        $query->setParameter(1, $id);
        $nonInscrit = $query->getResult();


        if(!$sortie){
            throw new NotFoundHttpException("This sortie doesn't exist");
        }
        return $this->render('sortie/detail.html.twig',
            compact("id",'sortie','idUserCourant','nonInscrit'));
    }


    #[Route('/sortie/modified/{id}', name: self::ROUTE_MODIFIED_SORTIE,requirements: ['id'=>'\d+'])]
    public function modified($id, SortieRepository $SortiesRepository, Request $request, EntityManagerInterface $entityManager): Response
    {

        $sortie = $SortiesRepository->find($id);

        if(!$sortie){
            throw new NotFoundHttpException("This sortie doesn't exist");
        }else {
            // Creation d'un formulaire en fonction d'un wish
            $form = $this->createForm(SortieType::class, $sortie);

            // Recupere ce qui été envoyé
            $form->handleRequest($request);

            // Check si le formulaire est valide et envoyé
            if ($form->isSubmitted() && $form->isValid()) {

                $this->extracted($form, $sortie);

                $entityManager->persist($sortie);
                $entityManager->flush();
                // Message flash
                $this->addFlash('success', 'Sortie successfully modified !');
                // Redirection vers la page des Sortie
                return $this->redirectToRoute(self::ROUTE_SORTIE);
            }
            // Affichage du formulaire
            $sortieForm = $form->createView();
        }
        return $this->render('sortie/modified.html.twig',
            compact("id",'sortie','sortieForm'));
    }

    #[Route('/sortie/annule/{id}', name: self::ROUTE_ANNULE_SORTIE,requirements: ['id'=>'\d+'])]
    public function annule(
        $id,
        SortieRepository $SortiesRepository,
        EtatRepository $etatsRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ) {


        $sortie = $SortiesRepository->find($id);
        $etatsAnnule = $etatsRepository->findOneBy([
            'libelle' => 'Annulée'
        ]);

        if(!$sortie){
            throw new NotFoundHttpException("This sortie doesn't exist");
        }else {
            $form = $this->createForm(SortieAnnuleType::class,$sortie);
            $form->handleRequest($request);

            // Check si le formulaire est valide et envoyé
            if ($form->isSubmitted() && $form->isValid()) {

                $sortie->setEtat($etatsAnnule);
                $entityManager->persist($sortie);
                $entityManager->flush();

                // Message flash
                $this->addFlash('success', 'Sortie successfully annulé !');
                // Redirection vers la page des Sortie
                return $this->redirectToRoute(self::ROUTE_SORTIE);
            }
            // Affichage du formulaire
            $sortieForm = $form->createView();
        }
        return $this->render('sortie/annule.html.twig',
            compact("id",'sortie','sortieForm'));
    }

    #[Route('/sortie', name: self::ROUTE_SORTIE)]
    public function index(?UserInterface $userCourant, Request $request, SortieRepository $SortiesRepository): Response
    {


        /** @var Participant $userCourant */
        $lesSorties = $SortiesRepository->findRecherche();
        //dd($userCourant);

        $response = new Response(
            'Content',
            Response::HTTP_OK,
            ['content-type' => 'text/html']
        );
        //dd($userCourant->getPhoto());
        $response->headers->setCookie(Cookie::create('profilImg', $userCourant->getPhoto()));

        return $this->render('sortie/index.html.twig', [
            'lesSorties' => $lesSorties,
            'idUserCourant'=>$userCourant->getId(),
        ],
        $response);
    }


    #[Route('/sortieRecherche', name: self::ROUTE_SORTIE_RECHERCHER)]
    public function indexRecherche(?UserInterface $userCourant, Request $request, SortieRepository $SortiesRepository): Response
    {
        
        $sortieOrgan=$request->get('sortieOrgan');
        $sortieInscit=$request->get('sortieInscit');
        $sortieNonInscit=$request->get('sortieNonInscit');
        $sortiePasse=$request->get('sortiePasse');
        $idUserCourant=$userCourant->getId();

        if ($sortieOrgan===null && $sortieInscit===null && $sortieNonInscit===null && $sortiePasse===null) {

            return $this->redirectToRoute(self::ROUTE_SORTIE);
        }
       $lesSorties=$SortiesRepository->findRechercheCheckBox($sortieOrgan,$sortieInscit,$sortieNonInscit,$sortiePasse,$idUserCourant);
     
        return $this->render('sortie/index.html.twig', [
            'lesSorties' => $lesSorties,
            'idUserCourant'=>$idUserCourant,
            'sortieOrgan'=>$sortieOrgan,
            'sortieNonInscit'=>$sortieNonInscit,
            'sortiePasse'=>$sortiePasse,
            'sortieInscit'=>$sortieInscit
        ]);
    }

    /**
     * @throws \Exception
     */
    #[Route('/inscription/sortie/{id}', name: self::ROUTE_INSCRIPTION_SORTIE, requirements: ['id'=>'\d+'])]
    #[Route('/desinscription/sortie/{id}', name: self::ROUTE_DESINSCRIPTION_SORTIE, requirements: ['id'=>'\d+'])]
    public function inscriptionDesinscriptionSortie(?UserInterface $userCourant, Request $request): Response
    {
        /** @var Participant $userCourant */
        if (is_null($userCourant)) {
            throw new AccessDeniedException('Veuillez vous connecter pour vous inscrire à une activité !');
        }

        $sortie = $this->em->getRepository(Sortie::class)->find($request->get('id'));

        if (is_null($sortie)) {
            throw new AccessDeniedException('La sortie n\'a pas été trouvé, veuillez réessayer');
        }

        $message = '';
        try {
            if ($request->get('_route') === self::ROUTE_INSCRIPTION_SORTIE) {
                $success = $this->serviceSortie->inscrireSortie($userCourant, $sortie);
                $message = "Votre inscription a été prise en compte !";
            } elseif ($request->get('_route') === self::ROUTE_DESINSCRIPTION_SORTIE) {
                $success = $this->serviceSortie->desisterSortie($userCourant, $sortie);
                $message = "Votre dé-inscription a été prise en compte !";
            } else {
                throw new \Exception("Une erreur s'est produite, veuillez réessayer !");
            }
        } catch (Exception $e) {
            $success = false;
        }

        $message = $success === false
            ? "Votre action n'a pas été prise en compte, réessayer"
            : $message;

        $type = $success === true
            ? "success"
            : "error";

        $this->addFlash($type, $message);

        return $this->redirectToRoute(self::ROUTE_SORTIE);
    }

    /**
     * @param FormInterface $form
     * @param Sortie $sortie
     * @return void
     */
    public function extracted(FormInterface $form, Sortie $sortie): void
    {
        $imageSortie = $form->get('image')->getData();

        if ($imageSortie) {
            $OriFilename = pathinfo($imageSortie->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $this->slugger->slug($OriFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageSortie->guessExtension();
            try {
                $imageSortie->move(
                    $this->getParameter('profile_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
            }
            $sortie->setUrlphoto($newFilename);
        }
    }
}