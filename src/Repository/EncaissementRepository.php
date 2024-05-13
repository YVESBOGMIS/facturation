<?php

namespace App\Repository;

use App\Entity\Encaissement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Encaissement>
 *
 * @method Encaissement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Encaissement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Encaissement[]    findAll()
 * @method Encaissement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EncaissementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Encaissement::class);
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


    public function findEncaisementGlobalSurPeriode(QueryBuilder $queryBuilder, $user, $dateDebut, $dateFin)
    {
        // Construire la requête pour obtenir les montants des encaissements de la vente spécifiée dans la période spécifiée
        $queryBuilder
            ->select('SUM(e.Montant) as montant_total')
            ->join('e.vente', 'v')
            ->where('v.user = :user')
            ->setParameter('user', $user);

        // Ajouter la condition de plage de dates
        $this->addDateRangeCondition($queryBuilder, $dateDebut, $dateFin);

        // Exécuter la requête et retourner le résultat
        return $queryBuilder->getQuery()->getSingleScalarResult();
    }

    public function findByTopClientsByUser($user, $dateDebut, $dateFin)
    {
        $queryBuilder = $this->createQueryBuilder('e');

        // Ajouter la condition de plage de dates
        $this->addDateRangeCondition($queryBuilder, $dateDebut, $dateFin);

        return $queryBuilder
            ->select('c.id', 'c.Nom AS nom_client', 'c.Prenom',
                'SUM(e.Montant) AS montant_total_encaissement_pour_ce_client',
            )
            ->leftJoin('e.vente', 'v')
            ->leftJoin('v.client', 'c')
            ->where('v.user = :user')
            ->andWhere('v.etat_de_vente = :etat_de_vente')
            ->setParameter('user', $user)
            ->setParameter('etat_de_vente', 7) // Assurez-vous que 7 est le bon état pour "encaisser"
            ->groupBy('c.id')
            ->orderBy('montant_total_encaissement_pour_ce_client', 'DESC')
            ->getQuery()
            ->getResult();
    }



    //    /**
    //     * @return Encaissement[] Returns an array of Encaissement objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Encaissement
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
