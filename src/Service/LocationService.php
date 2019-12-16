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
    private $cityRepository;

    public function __construct(
        CountryRepository $countryRepository,
        AddressRepository $addressRepository,
        CityRepository $cityRepository
    ) {
        $this->countryRepository = $countryRepository;
        $this->addressRepository = $addressRepository;
        $this->cityRepository = $cityRepository;
    }

    /**
     * @param $search
     * @return Country[]
     */
    public function findCountriesByNameOr404(?string $search): array
    {
        if ($search != null || strlen($search) > 0) {
            $countries = $this->countryRepository->findByNameLike($search);
        } else {
            $countries = $this->countryRepository->findBy([], ['priority' => 'DESC', 'frName' => 'ASC']);
        }
        if (count($countries) === 0) {
            throw new NotFoundHttpException("No countries found.");
        }
        return $countries;
    }

    public function findCitiesByNameOr404(int $countryId, ?string $search)
    {
        $country = $this->countryRepository->findOneBy(['id' => $countryId]);
        if ($country == null) {
            throw new NotFoundHttpException("Country id does not exist.");
        }

        if ($search != null || strlen($search) > 0) {
            $cities = $this->cityRepository->findByNameOrZipCodeLike($country, $search);
        } else {
            $cities = $country->getCities();
        }
        if (count($cities)) {
            throw new NotFoundHttpException("No cities found.");
        }
        return $cities;
    }

    public function findCountryByCode(string $code): ?Country
    {
        return $this->countryRepository->findOneBy(['code' => strtoupper($code)]);
    }
}
