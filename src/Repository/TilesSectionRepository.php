<?php

namespace App\Repository;

use App\Entity\TilesSection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method TilesSection|null find($id, $lockMode = null, $lockVersion = null)
 * @method TilesSection|null findOneBy(array $criteria, array $orderBy = null)
 * @method TilesSection[]    findAll()
 * @method TilesSection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TilesSectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TilesSection::class);
    }

    // /**
    //  * @return TilesSection[] Returns an array of TilesSection objects
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
    public function findOneBySomeField($value): ?TilesSection
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
