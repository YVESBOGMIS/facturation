<?php

namespace App\Repository;

use App\Entity\LigneDeVente;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LigneDeVente>
 *
 * @method LigneDeVente|null find($id, $lockMode = null, $lockVersion = null)
 * @method LigneDeVente|null findOneBy(array $criteria, array $orderBy = null)
 * @method LigneDeVente[]    findAll()
 * @method LigneDeVente[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LigneDeVenteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LigneDeVente::class);
    }

    //    /**
    //     * @return LigneDeVente[] Returns an array of LigneDeVente objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('l.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?LigneDeVente
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
