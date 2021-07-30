<?php

namespace App\Repository;

use App\Entity\PurchaseSuspicion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PurchaseSuspicion|null find($id, $lockMode = null, $lockVersion = null)
 * @method PurchaseSuspicion|null findOneBy(array $criteria, array $orderBy = null)
 * @method PurchaseSuspicion[]    findAll()
 * @method PurchaseSuspicion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PurchaseSuspicionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PurchaseSuspicion::class);
    }

    // /**
    //  * @return PurchaseSuspicion[] Returns an array of PurchaseSuspicion objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PurchaseSuspicion
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
