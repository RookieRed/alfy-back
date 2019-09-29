<?php

namespace App\Repository;

use App\Entity\PageFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PageFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method PageFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method PageFile[]    findAll()
 * @method PageFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageFileRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PageFile::class);
    }

    // /**
    //  * @return FilePage[] Returns an array of FilePage objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FilePage
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
