<?php

namespace App\Repository;

use App\Entity\HTMLSection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method HTMLSection|null find($id, $lockMode = null, $lockVersion = null)
 * @method HTMLSection|null findOneBy(array $criteria, array $orderBy = null)
 * @method HTMLSection[]    findAll()
 * @method HTMLSection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HTMLSectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HTMLSection::class);
    }
}
