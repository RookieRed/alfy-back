<?php

namespace App\Repository;

use App\Entity\TimelineEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method TimelineEvent|null find($id, $lockMode = null, $lockVersion = null)
 * @method TimelineEvent|null findOneBy(array $criteria, array $orderBy = null)
 * @method TimelineEvent[]    findAll()
 * @method TimelineEvent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TimelineEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TimelineEvent::class);
    }

    // /**
    //  * @return TimelineEvent[] Returns an array of TimelineEvent objects
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
    public function findOneBySomeField($value): ?TimelineEvent
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
