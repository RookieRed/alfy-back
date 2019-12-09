<?php
/**
 * Created by PhpStorm.
 * User: celoundou-ngah
 * Date: 01/08/2018
 * Time: 16:12
 */

namespace App\Controller;


use App\Repository\CountryRepository;
use App\Service\LocationService;
use App\Utils\JsonSerializer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LocationController extends JsonAbstractController
{
    private $em;
    private $locationService;

    public function __construct(
        JsonSerializer $serializer,
        EntityManagerInterface $em,
        LocationService $locationService
    ) {
        parent::__construct($serializer);
        $this->em = $em;
        $this->locationService = $locationService;
    }

    /**
     * @Route(path="/countries", methods={"GET"}, name="countries_list")
     */
    public function getCountries(Request $request)
    {
        $search = $request->get('search');
        $countries = $this->locationService->findCountriesByNameOr404($search);
        return $this->json($countries);
    }

    /**
     * @Route(path="/countries/{id}/cities", methods={"GET"}, name="country_towns_list")
     */
    public function getTowns(Request $request)
    {
        $search = $request->get('search');
        $countries = $this->locationService->findTownsByNameOr404($search);
        return $this->json($countries);
    }
}