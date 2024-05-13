<?php

namespace App\Repository;

use App\Entity\DocumentAchat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DocumentAchat>
 *
 * @method DocumentAchat|null find($id, $lockMode = null, $lockVersion = null)
 * @method DocumentAchat|null findOneBy(array $criteria, array $orderBy = null)
 * @method DocumentAchat[]    findAll()
 * @method DocumentAchat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentAchatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocumentAchat::class);
    }

    //    /**
    //     * @return DocumentAchat[] Returns an array of DocumentAchat objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('d.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?DocumentAchat
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
