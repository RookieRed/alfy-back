<?php

namespace App\Repository;

use App\Entity\EventTile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method EventTile|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventTile|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventTile[]    findAll()
 * @method EventTile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventTileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventTile::class);
    }

    // /**
    //  * @return SectionEvent[] Returns an array of SectionEvent objects
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
    public function findOneBySomeField($value): ?SectionEvent
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
