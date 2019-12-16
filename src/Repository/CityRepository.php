<?php

namespace App\Repository;

use App\Entity\Country;
use App\Entity\City;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method City|null find($id, $lockMode = null, $lockVersion = null)
 * @method City|null findOneBy(array $criteria, array $orderBy = null)
 * @method City[]    findAll()
 * @method City[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, City::class);
    }

    public function findByNameOrZipCodeLike(Country $country, string $search)
    {
        return $this->getEntityManager()->createQuery("SELECT t FROM App\Entity\City t "
            ." WHERE t.country = :country AND (t.zipCode LIKE :search OR t.name LIKE :search)")
            ->execute(['search' => $search . '%', 'country' => $country]);
    }
}
