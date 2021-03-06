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
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as Doc;

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
     * @Doc\Tag(name="Localisation", description="Lite des pays, villes, gestion des adresses.")
     * @Doc\Response(response=200, description="Liste des pays.",
     *      @Model(type="App\Entity\Country", groups={"user_get"}))
     */
    public function getCountries(Request $request)
    {
        $search = $request->get('search');
        $countries = $this->locationService->findCountriesByNameOr404($search);
        return $this->json($countries);
    }

    /**
     * @Route(path="/countries/{idCountry}/cities", methods={"GET"}, name="country_cities_list")
     * @Doc\Tag(name="Localisation", description="Lite des pays, villes, gestion des adresses.")
     * @Doc\Response(response=200, description="Liste des villes du pays en paramètre",
     *      @Model(type="App\Entity\City", groups={"location_read"}))
     * @Doc\Response(response=404, description="Pays non trouvé.")
     */
    public function getCities(Request $request)
    {
        $search = $request->get('search');
        $idCountry = +$request->get('idCountry');
        $countries = $this->locationService->findCitiesByNameOr404($idCountry, $search);
        return $this->json($countries);
    }

    /**
     * @Route(path="/countries/{idCountry}/cities", methods={"POST"}, name="create_city")
     * @Doc\Tag(name="Localisation", description="Lite des pays, villes, gestion des adresses.")
     * @Doc\Response(response=200, description="[A CHANGER] OK")
     */
    public function createNewCity(Request $request)
    {
        // TODO
    }
}