<?php

namespace App\Repository;

use App\Entity\QuestionAnswered;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method QuestionAnswered|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuestionAnswered|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuestionAnswered[]    findAll()
 * @method QuestionAnswered[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionAnsweredRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuestionAnswered::class);
    }
}
