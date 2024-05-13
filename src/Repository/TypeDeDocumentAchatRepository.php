<?php

namespace App\Repository;

use App\Entity\TypeDeDocumentAchat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TypeDeDocumentAchat>
 *
 * @method TypeDeDocumentAchat|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeDeDocumentAchat|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeDeDocumentAchat[]    findAll()
 * @method TypeDeDocumentAchat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeDeDocumentAchatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeDeDocumentAchat::class);
    }

    //    /**
    //     * @return TypeDeDocumentAchat[] Returns an array of TypeDeDocumentAchat objects
    //     */
    //    public function findByExam    pleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?TypeDeDocumentAchat
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
