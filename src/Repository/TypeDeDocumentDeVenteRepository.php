<?php

namespace App\Repository;

use App\Entity\TypeDeDocumentDeVente;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TypeDeDocumentDeVente>
 *
 * @method TypeDeDocumentDeVente|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeDeDocumentDeVente|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeDeDocumentDeVente[]    findAll()
 * @method TypeDeDocumentDeVente[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeDeDocumentDeVenteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeDeDocumentDeVente::class);
    }

    //    /**
    //     * @return TypeDeDocumentDeVente[] Returns an array of TypeDeDocumentDeVente objects
    //     */
    //    public function findByExampleField($value): array
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

    //    public function findOneBySomeField($value): ?TypeDeDocumentDeVente
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
