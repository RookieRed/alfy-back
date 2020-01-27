<?php

namespace App\Repository;

use App\Entity\FAQCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method FAQCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method FAQCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method FAQCategory[]    findAll()
 * @method FAQCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FAQCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FAQCategory::class);
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
