<?php

namespace App\Repository;

use App\Entity\CategoryFAQ;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method CategoryFAQ|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategoryFAQ|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategoryFAQ[]    findAll()
 * @method CategoryFAQ[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryFAQRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategoryFAQ::class);
    }

    // /**
    //  * @return CategoryFAQ[] Returns an array of CategoryFAQ objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CategoryFAQ
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
