<?php

namespace App\Service;

use App\Entity\Participant;
use App\Entity\Sortie;
use Doctrine\ORM\EntityManagerInterface;

class SortieService
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function inscrireSortie(?Participant $userCourant, ?Sortie $sortie) :bool
    {
        $success = false;
        $utilisateurBdd = $this->em->getRepository(Participant::class)->find($userCourant->getId());
        $sortieBdd = $this->em->getRepository(Sortie::class)->find($sortie->getId());

        if (
            ($sortieBdd->getEtat()->getLibelle() === Sortie::ETAT_CREEE || $sortieBdd->getEtat()->getLibelle() === Sortie::ETAT_OUVERTE)
            && ($sortieBdd->getDateDebut()->format('d/m/Y') >  (new \DateTime())->format('d/m/Y'))
            && ($sortieBdd->getDateCloture()->format('d/m/Y') >  (new \DateTime())->format('d/m/Y'))
            && $sortieBdd->getNbInscriptionsMax() > count($sortieBdd->getParticipants())
        ) {
            $utilisateurBdd->addSortie($sortieBdd);
            $this->em->flush();
            $success = true;
        }

        return $success;
    }

    public function desisterSortie(?Participant $userCourant, ?Sortie $sortie) :bool
    {
        $success = false;
        $utilisateurBdd = $this->em->getRepository(Participant::class)->find($userCourant->getId());
        $sortieBdd = $this->em->getRepository(Sortie::class)->find($sortie->getId());

        if (
            ($sortieBdd->getEtat()->getLibelle() === Sortie::ETAT_CREEE || $sortieBdd->getEtat()->getLibelle() === Sortie::ETAT_OUVERTE)
            && ($sortieBdd->getDateDebut()->format('d/m/Y') >  (new \DateTime())->format('d/m/Y')))
        {
            $utilisateurBdd->removeSortie($sortieBdd);
            $this->em->flush();
            $success = true;
        }

        return $success;
    }
}