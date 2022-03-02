<?php

namespace App\Repository;

use App\Entity\Etats;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Etats|null find($id, $lockMode = null, $lockVersion = null)
 * @method Etats|null findOneBy(array $criteria, array $orderBy = null)
 * @method Etats[]    findAll()
 * @method Etats[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EtatsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Etats::class);
    }

    public function add(Etats $entity, bool $flush = true): void
    {
        $d = new \DateTime();
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function remove(Etats $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findAnnulee() {
        //QueryBuilder
        $qb = $this->createQueryBuilder('e');
        $s = 'e.libelle = '."'Annulée'";
        $qb->andWhere($s);
        $query = $qb->getQuery();
        return $query->getResult();
    }

    public function findCree() {
        //QueryBuilder
        $qb = $this->createQueryBuilder('e');
        $s = 'e.libelle = '."'Créée'";
        $qb->andWhere($s);
        $query = $qb->getQuery();
        return $query->getResult();
    }

    public function findOuverte() {
        //QueryBuilder
        $qb = $this->createQueryBuilder('e');
        $s = 'e.libelle = '."'Ouverte'";
        $qb->andWhere($s);
        $query = $qb->getQuery();
        return $query->getResult();
    }
}
