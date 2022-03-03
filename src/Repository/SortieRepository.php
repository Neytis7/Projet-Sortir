<?php

namespace App\Repository;

use App\Entity\Sortie;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function insertInscription(int $participantId, int $sortieId) :bool
    {
        dd('not insert inscription implement');
    }

    public function findRecherche() {
        $date=date("Y-m-d H:m:s");
        $date=date('Y-m-d H:m:s', strtotime($date. ' - 1 month'));
        $datetime = DateTime::createFromFormat('Y-m-d H:m:s', $date);

        $qb = $this->createQueryBuilder('sortie');
        $qb
            ->select('sortie')
            ->addSelect('organisateur')
            ->addSelect('lieu')
            ->addSelect('participant')

            ->leftJoin('sortie.organisateur','organisateur')
            ->leftJoin('sortie.lieuxNoLieu','lieu')
            ->leftJoin('sortie.participantsNoParticipant','participant')


            ->where("sortie.datedebut > :datetime")
            ->setParameter('datetime', $datetime)

            ->orderBy('sortie.noSortie', 'DESC');

        return $qb->getQuery()->getResult();
    }
}
