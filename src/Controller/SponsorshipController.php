<?php
/**
 * Created by PhpStorm.
 * User: celoundou-ngah
 * Date: 21/06/2018
 * Time: 13:39
 */

namespace App\Controller;


use App\Utils\JsonSerializer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SponsorshipController
 * @package App\Controller
 *
 * @Route(path="/sponsorship")
 */
class SponsorshipController extends JsonAbstractController
{
    public function __construct(JsonSerializer $serializer)
    {
        parent::__construct($serializer);
    }

    /**
     * @Route(path="/{whos}",
     *     methods={"GET"},
     *     name="sponsor_get_by_user",
     *     requirements={"whos"="^(\d+|mine)$"}
     * )
     */
    public function getAllSponsored()
    {
    }

    /**
     * @Route(path="/free",
     *     name="sponsor_get_not_sponsored",
     *     methods={"GET"})
     */
    public function getNotSponsored()
    {

    }

    /**
     * @Route(path="",
     *     name="sponsor_update",
     *     methods={"POST"})
     */
    public function updateSponsoredList()
    {
    }
}