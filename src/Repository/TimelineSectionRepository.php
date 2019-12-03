<?php

namespace App\Repository;

use App\Entity\TimelineSection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method TimelineSection|null find($id, $lockMode = null, $lockVersion = null)
 * @method TimelineSection|null findOneBy(array $criteria, array $orderBy = null)
 * @method TimelineSection[]    findAll()
 * @method TimelineSection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TimelineSectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TimelineSection::class);
    }

    // /**
    //  * @return TimelineSection[] Returns an array of TimelineSection objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TimelineSection
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
