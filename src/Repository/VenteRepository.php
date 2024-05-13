<?php

namespace App\Repository;

use App\Entity\Vente;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;




/**
 * @extends ServiceEntityRepository<Vente>
 *
 * @method Vente|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vente|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vente[]    findAll()
 * @method Vente[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VenteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vente::class);
    }

    public function addDateRangeCondition(QueryBuilder $qb, $dateDebut = null, $dateFin = null)
    {
        if ($dateDebut && $dateFin) {
            // Convertir les chaînes $dateDebut et $dateFin en objets DateTime
            $dateDebutObj = new \DateTime($dateDebut);
            $dateFinObj = new \DateTime($dateFin);

            // Ajouter un jour à la date de fin pour inclure toute la journée du dernier jour spécifié
            $dateFinObj->modify('+1 day');

            $qb->andWhere('v.date_de_vente BETWEEN :dateDebut AND :dateFin')
                ->setParameter('dateDebut', $dateDebutObj)
                ->setParameter('dateFin', $dateFinObj);
        } elseif ($dateDebut) {
            // Si seule la date de début est spécifiée, inclure toutes les ventes à partir de cette date jusqu'à la date la plus récente
            $qb->andWhere('v.date_de_vente >= :dateDebut')
                ->setParameter('dateDebut', new \DateTime($dateDebut));
        } elseif ($dateFin) {
            // Si seule la date de fin est spécifiée, inclure toutes les ventes jusqu'à cette date, y compris celle-ci
            $dateFinObj = new \DateTime($dateFin);
            $dateFinObj->modify('+1 day'); // Ajouter un jour pour inclure toute la journée de la date de fin
            $qb->andWhere('v.date_de_vente < :dateFin')
                ->setParameter('dateFin', $dateFinObj);
        }
    }


    public function countVentesSurPeriode($user, $dateDebut = null, $dateFin = null)
    {
        $qb = $this->createQueryBuilder('v')
            ->select('COUNT(v.id)')
            ->where('v.user = :user')
            ->setParameter('user', $user);

        // Ajouter la condition de plage de dates
        $this->addDateRangeCondition($qb, $dateDebut, $dateFin);

        // Exécuter la requête et retourner le résultat
        return $qb->getQuery()->getSingleScalarResult();
    }



    public function findByEtatDeVenteId($id)
    {
        return $this->createQueryBuilder('v')
            ->leftJoin('v.etat_de_vente', 'e')
            ->andWhere('e.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }


    public function findVentesEncaisseesByClient( $id)
    {
        return $this->createQueryBuilder('v')
            ->join('v.etat_de_vente', 'etat')
            ->andWhere('etat.Slug = :EN') // Supposons que "encaissé" est le nom de l'état encaissé
            ->setParameter('EN', 'encaisser')
            ->andWhere('v.client = :client')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }


    public function getTotalVentes($user, $dateDebut = null, $dateFin = null): int
    {
        $qb = $this->createQueryBuilder('v')
            ->select('COUNT(v.id)')
            ->where('v.user = :user')
            ->setParameter('user', $user);

        // Ajouter la condition de plage de dates
        $this->addDateRangeCondition($qb, $dateDebut, $dateFin);

        // Exécuter la requête et retourner le résultat
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getVentesAbouties($user, $dateDebut = null, $dateFin = null): int
    {
        $qb = $this->createQueryBuilder('v')
            ->select('COUNT(v.id)')
            ->where('v.user = :user')
            ->andWhere('v.etat_de_vente = :etat_id')
            ->setParameter('user', $user)
            ->setParameter('etat_id', 7);


        // Ajouter la condition de plage de dates
        $this->addDateRangeCondition($qb, $dateDebut, $dateFin);

        // Exécuter la requête et retourner le résultat
        return $qb->getQuery()->getSingleScalarResult();

    }

    //    /**
    //     * @return Vente[] Returns an array of Vente objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('v.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Vente
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
