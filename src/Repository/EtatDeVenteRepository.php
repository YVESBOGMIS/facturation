<?php

namespace App\Repository;

use App\Entity\EtatDeVente;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EtatDeVente>
 *
 * @method EtatDeVente|null find($id, $lockMode = null, $lockVersion = null)
 * @method EtatDeVente|null findOneBy(array $criteria, array $orderBy = null)
 * @method EtatDeVente[]    findAll()
 * @method EtatDeVente[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EtatDeVenteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EtatDeVente::class);
    }

    //    /**
    //     * @return EtatDeVente[] Returns an array of EtatDeVente objects
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

    //    public function findOneBySomeField($value): ?EtatDeVente
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
