<?php

namespace App\Repository;

use App\Entity\Requisition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Requisition|null find($id, $lockMode = null, $lockVersion = null)
 * @method Requisition|null findOneBy(array $criteria, array $orderBy = null)
 * @method Requisition[]    findAll()
 * @method Requisition[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RequisitionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Requisition::class);
    }

    // /**
    //  * @return Requisition[] Returns an array of Requisition objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Requisition
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
