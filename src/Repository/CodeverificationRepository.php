<?php

namespace App\Repository;

use App\Entity\Codeverification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Codeverification>
 *
 * @method Codeverification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Codeverification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Codeverification[]    findAll()
 * @method Codeverification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CodeverificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Codeverification::class);
    }

    //    /**
    //     * @return Codeverification[] Returns an array of Codeverification objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Codeverification
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
