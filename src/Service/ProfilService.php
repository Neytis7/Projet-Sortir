<?php

namespace App\Service;

use App\Entity\Participant;
use Doctrine\ORM\EntityManagerInterface;

class ProfilService
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     *
     * @param int $id
     * @return Participant|null
     */
    public function getParticipantById(int $id) : ?Participant
    {
        return $this->em->getRepository(Participant::class)->find($id);
    }
}