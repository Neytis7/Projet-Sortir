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
     * Verifie qu'un pseudo est unique
     *
     * @param string $pseudo
     * @return bool
     */
    public function estPseudoUnique(string $pseudo) : bool
    {
        $estUnique = false;
        $pseudo = trim($pseudo);
        $allPseudos = $this->em->getRepository(Participants::class)->findBy([
            'pseudo' => $pseudo
        ]);

        if (count($allPseudos) <= 0) {
            $estUnique = true;
        }

        return $estUnique;
    }
}