<?php
/**
 * Created by PhpStorm.
 * User: celoundou-ngah
 * Date: 01/08/2018
 * Time: 16:12
 */

namespace App\Controller;


use App\Repository\CountryRepository;
use App\Utils\JsonSerializer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AddressController extends JsonAbstractController
{
    private $em;
    private $countryRepo;

    public function __construct(
        JsonSerializer $serializer,
        EntityManagerInterface $em,
        CountryRepository $countryRepo
    )
    {
        parent::__construct($serializer);
        $this->em = $em;
        $this->countryRepo = $countryRepo;
    }

    /**
     * @Route(path="/countries", methods={"GET"}, name="countries_list")
     */
    public function getCountries(Request $request)
    {
        $search = $request->get('search');

        if ($search != null) {
            $countries = $this->countryRepo->searchBy($search);
        } else {
            $countries = $this->countryRepo->findBy([], ['priority' => 'DESC', 'frName' => 'ASC']);
        }
        return $this->json($countries);
    }
}