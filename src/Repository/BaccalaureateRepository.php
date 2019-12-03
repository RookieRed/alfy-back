<?php

namespace App\Repository;

use App\Entity\Baccalaureate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Baccalaureate|null find($id, $lockMode = null, $lockVersion = null)
 * @method Baccalaureate|null findOneBy(array $criteria, array $orderBy = null)
 * @method Baccalaureate[]    findAll()
 * @method Baccalaureate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BaccalaureateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Baccalaureate::class);
    }

//    /**
//     * @return Baccalaureate[] Returns an array of Baccalaureate objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Baccalaureate
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
