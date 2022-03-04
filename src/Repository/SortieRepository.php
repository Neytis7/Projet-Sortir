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
            ->leftJoin('sortie.lieu','lieu')
            ->leftJoin('sortie.participants','participant')


            ->where("sortie.dateDebut > :datetime")
            ->setParameter('datetime', $datetime)

            ->orderBy('sortie.id', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function findRechercheCheckBox($sortieOrgan,$sortieInscit,$sortieNonInscit,$sortiePasse, $idUserCourant) {
        
        $date=date("Y-m-d H:m:s");
        $dateMoinsUnMois=date('Y-m-d H:m:s', strtotime($date. ' - 1 month'));
        $dateTimeMoinsUnMois = DateTime::createFromFormat('Y-m-d H:m:s', $dateMoinsUnMois);

        $dateJour=date('Y-m-d H:m:s', strtotime($date));
        $dateTimeJour = DateTime::createFromFormat('Y-m-d H:m:s', $dateJour);

        $qbImbrique = $this->createQueryBuilder('sortie');
        $qbImbrique->select('sortie.id')

                    ->join('sortie.participants','participant')
                    ->andWhere('participant.id = :idParticipantNonInscrit')
                    ->setParameter('idParticipantNonInscrit', $idUserCourant);

        $qb = $this->createQueryBuilder('sortie');
        $qb
            ->select('sortie')
            ->addSelect('organisateur')
            ->addSelect('lieu')
            ->addSelect('participant')

            ->leftJoin('sortie.organisateur','organisateur')
            ->leftJoin('sortie.lieu','lieu')
            ->leftJoin('sortie.participants','participant');

            if ($sortieOrgan!==null) {
                $qb->andWhere("organisateur.id = :idOrganisateur")
                ->setParameter('idOrganisateur', $idUserCourant);
            }
            if ($sortieInscit!==null) {
                $qb->andWhere("participant.id = :idParticipant")
                ->setParameter('idParticipant', $idUserCourant);
            }

            if ($sortieNonInscit!==null) {
                $lesIdDesSortiesUtilisateurParticipe=$qbImbrique->getQuery()->getResult();
                if (count($lesIdDesSortiesUtilisateurParticipe)>0) {
                    # code...
                    // $qb->andWhere("sortie.id not IN ()");
                    $qb->andWhere("sortie.id not IN (:requete)")
                    ->setParameter('requete', $qbImbrique->getQuery()->getResult());
                }
                
            }
            // if ($sortieNonInscit!==null) {
            //     // dd($qbImbrique->getQuery()->getResult());
            //     $qb->expr()->notIn('sortie.id', $qbImbrique->getQuery()->getDQL());
            //     // $qb->expr()->notIn('sortie.id', 1);

            // }

                
            if ($sortiePasse!==null) {
                $qb->andWhere("sortie.dateDebut <= :dateTimeJour")
                ->setParameter('dateTimeJour', $dateTimeJour);
            }
            else{

                $qb->andWhere("sortie.dateDebut >= :datetime")
                ->setParameter('datetime', $dateTimeMoinsUnMois);
            }

            $qb->orderBy('sortie.id', 'DESC');
            //  dd($qb->getQuery()->getSQL(), $qb->getQuery()->getParameters());
            // dd($qb->getQuery()->getResult());

            
        return $qb->getQuery()->getResult();
    }
}