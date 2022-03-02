<?php

namespace App\Service;

use App\Entity\Participants;
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
     * @return Participants|null
     */
    public function getParticipantById(int $id) : ?Participants
    {
        return $this->em->getRepository(Participants::class)->find($id);
    }
}