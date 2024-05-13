<?php

namespace App\Repository;

use App\Entity\EtatAchat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EtatAchat>
 *
 * @method EtatAchat|null find($id, $lockMode = null, $lockVersion = null)
 * @method EtatAchat|null findOneBy(array $criteria, array $orderBy = null)
 * @method EtatAchat[]    findAll()
 * @method EtatAchat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EtatAchatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EtatAchat::class);
    }

    //    /**
    //     * @return EtatAchat[] Returns an array of EtatAchat objects
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

    //    public function findOneBySomeField($value): ?EtatAchat
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
