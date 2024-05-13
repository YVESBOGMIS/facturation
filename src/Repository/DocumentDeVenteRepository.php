<?php

namespace App\Repository;

use App\Entity\DocumentDeVente;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DocumentDeVente>
 *
 * @method DocumentDeVente|null find($id, $lockMode = null, $lockVersion = null)
 * @method DocumentDeVente|null findOneBy(array $criteria, array $orderBy = null)
 * @method DocumentDeVente[]    findAll()
 * @method DocumentDeVente[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentDeVenteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocumentDeVente::class);
    }

    //    /**
    //     * @return DocumentDeVente[] Returns an array of DocumentDeVente objects
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

    //    public function findOneBySomeField($value): ?DocumentDeVente
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
