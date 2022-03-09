<?php

namespace App\Repository;

use App\Entity\Participant;
use App\Entity\Sortie;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{

    private EntityManagerInterface $em;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em)
    {
        parent::__construct($registry, Sortie::class);
       $this->em = $em;
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
            ->addSelect('site')

            ->leftJoin('sortie.organisateur','organisateur')
            ->leftJoin('sortie.lieu','lieu')
            ->leftJoin('sortie.participants','participant')
            ->leftJoin('participant.site','site')


            ->where("sortie.dateDebut > :datetime")
            ->setParameter('datetime', $datetime)

            ->orderBy('sortie.id', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function findNonInscrit($idSortie, $userCourant){


        $rsm = new ResultSetMapping();
        $rsm->addEntityResult(Participant::class,'participant');
        $rsm->addFieldResult('participant', 'id', 'id');

        $rsm->addFieldResult('participant', 'pseudo', 'pseudo');
        $rsm->addFieldResult('participant', 'nom', 'nom');
        $rsm->addFieldResult('participant', 'prenom', 'prenom');

        $query = $this->em->createNativeQuery('SELECT participant.id, pseudo,nom,prenom FROM participant WHERE id NOT IN (SELECT participant_id FROM participant_sortie
                                           left join participant on participant.id = participant_id
                                           where sortie_id = ? ) and participant.id <> ?
        ', $rsm);

        $query->setParameter(1, $idSortie);
        $query->setParameter(2, $userCourant);
        return $query->getResult();
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
            if ($sortieInscit===null || $sortieNonInscit===null) {
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
            }

                
            if ($sortiePasse!==null) {
                $qb->andWhere("sortie.dateDebut <= :dateTimeJour")
                ->setParameter('dateTimeJour', $dateTimeJour);
            }
            else{

                $qb->andWhere("sortie.dateDebut >= :datetime")
                ->setParameter('datetime', $dateTimeMoinsUnMois);
            }

            $qb->orderBy('sortie.id', 'DESC');
            
        return $qb->getQuery()->getResult();
    }
}
