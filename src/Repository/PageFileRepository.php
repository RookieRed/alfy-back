<?php

namespace App\Repository;

use App\Entity\File;
use App\Entity\Page;
use App\Entity\PageFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PageFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method PageFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method PageFile[]    findAll()
 * @method PageFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageFileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PageFile::class);
    }

    public function findByPageOrFile($page = null, $file = null) {
        if ($page === null && $file === null) {
            return $this->findAll();
        }

        $criteria = [];
        if ($page !== null) {
            $criteria['page'] = $page;
        }
        if ($file !== null) {
            $criteria['file'] = $page;
        }
        if (count($criteria) === 2) {
            return $this->findOneBy($criteria);
        } else {
            return $this->findBy($criteria);
        }
    }
}
