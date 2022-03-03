<?php

namespace App\Repository;

use App\Entity\Sorties;
use App\Entity\Participants;
use App\Entity\Lieux;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sorties|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sorties|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sorties[]    findAll()
 * @method Sorties[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortiesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sorties::class);
    }

    public function findRecherche() {
        // $todayDate=date("Y-m-d");
        // $date=strtotime (date ( "Y-m-d", strtotime ( $todayDate )) . "-1 month" );
        // $dateTime=new DateTime;
        // dd($dateTime);
        $date=date("Y-m-d H:m:s");
        $date=date('Y-m-d H:m:s', strtotime($date. ' - 1 month'));
        $datetime = \DateTime::createFromFormat('Y-m-d H:m:s', $date);
        
       
  
        // $qb = $this->createQueryBuilder('s');
        // $qb->select('s.noSortie, o.nom as nomOrga, o.prenom, l.nomLieu, s.nom, w.datedebut, s.datecloture, s.nbinscriptionsmax, count(s.noSortie) as nombreSortie')
        //     ->leftJoin(Participants::class,'o','WITH','s.organisateur = o')
        //     ->leftJoin(Lieux::class,'l','WITH','s.lieuxNoLieu = l')
        //     ->leftJoin('s.inscriptions = i')
        //     ->andWhere("s.datedebut > :datetime")
        //     ->setParameter('datetime', $datetime)
        //     ->groupBy('s.noSortie')
        //     ->orderBy('s.noSortie', 'DESC');
        //     // ->join('w.category', 'c')
        //     // ->addSelect('c')
        //     // ->orderBy('w.dateCreated', 'DESC');
        //     // dd($qb);
        // $query = $qb->getQuery();
        // dd($query->getArrayResult());
        // return $query->getResult();
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
// dd($qb->getQuery()->getResult());
 
        return $qb->getQuery()->getResult();
    }

}
