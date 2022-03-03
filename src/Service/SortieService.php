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

    public function inscrireSortie(?Participant $userCourant, ?Sortie $sortie) :void
    {
        $utilisateurBdd = $this->em->getRepository(Participant::class)->find($userCourant->getId());
        $sortieBdd = $this->em->getRepository(Sortie::class)->find($sortie->getId());

        $utilisateurBdd->addSortie($sortieBdd);

        $this->em->flush();
    }

    public function desisterSortie(?Participant $userCourant, ?Sortie $sortie) :void
    {
        $utilisateurBdd = $this->em->getRepository(Participant::class)->find($userCourant->getId());
        $sortieBdd = $this->em->getRepository(Sortie::class)->find($sortie->getId());

        $utilisateurBdd->removeSortie($sortieBdd);

        $this->em->flush();
    }
}