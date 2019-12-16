<?php
/**
 * Created by PhpStorm.
 * User: celoundou-ngah
 * Date: 21/06/2018
 * Time: 13:39
 */

namespace App\Controller;

use App\Utils\JsonSerializer;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as Doc;

/**
 * Class SponsorshipController
 * @package App\Controller
 *
 * @Route(path="/sponsorships")
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
     *     requirements={"whos"="^(\d+|mine)$"})
     * @Doc\Tag(name="Parainages", description="Gestion des parainages.")
     * @Doc\Response(response=200, description="[A CHANGER] OK")
     */
    public function getAllSponsored()
    {
    }

    /**
     * @Route(path="/{whos}",
     *     name="sponsor_update",
     *     methods={"POST"},
     *     requirements={"whos"="^(\d+|mine)$"})
     * @Doc\Tag(name="Parainages", description="Gestion des parainages.")
     * @Doc\Response(response=200, description="[A CHANGER] OK")
     */
    public function updateSponsoredList()
    {
    }
}