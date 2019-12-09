<?php

namespace App\Service;

use App\Entity\Country;
use App\Repository\AddressRepository;
use App\Repository\CountryRepository;
use App\Repository\CityRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LocationService
{
    /**
     * @var CountryRepository
     */
    private $countryRepository;
    /**
     * @var AddressRepository
     */
    private $addressRepository;
    /**
     * @var CityRepository
     */
    private $townRepository;

    public function __construct(
        CountryRepository $countryRepository,
        AddressRepository $addressRepository,
        CityRepository $townRepository
    ) {
        $this->countryRepository = $countryRepository;
        $this->addressRepository = $addressRepository;
        $this->townRepository = $townRepository;
    }

    /**
     * @param $search
     * @return Country[]
     */
    public function findCountriesByNameOr404(string $search): array
    {
        if ($search != null || strlen($search) > 0) {
            $countries = $this->countryRepository->findByNameLike($search);
        } else {
            $countries = $this->countryRepository->findBy([], ['priority' => 'ASC', 'frName' => 'ASC']);
        }
        if (count($countries)) {
            throw new NotFoundHttpException("No countries found.");
        }
        return $countries;
    }

    public function findTownsByNameOr404(int $countryId, string $search)
    {
        $country = $this->countryRepository->findOneBy(['id' => $countryId]);
        if ($country == null) {
            throw new NotFoundHttpException("Country id does not exist.");
        }

        if ($search != null || strlen($search) > 0) {
            $towns = $this->townRepository->findByNameOrZipCodeLike($search);
        } else {
            $towns = $country->getTowns();
        }
        if (count($towns)) {
            throw new NotFoundHttpException("No towns found.");
        }
        return $towns;
    }
}
