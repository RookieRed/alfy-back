<?php
/**
 * Created by PhpStorm.
 * User: celoundou-ngah
 * Date: 21/06/2018
 * Time: 14:20
 */

namespace App\Controller;


use App\Utils\JsonSerializer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CareerController
 * @package App\Controller
 *
 * @Route(path="/careers")
 */
class CareerController extends JsonAbstractController
{
    public function __construct(JsonSerializer $serializer)
    {
        parent::__construct($serializer);
    }

    /**
     * @Route(path="",
     *     name="career_update",
     *     methods={"POST"})
     */
    public function update()
    {

    }

    /**
     * @Route(path="/{id}",
     *     name="career_by_user",
     *     methods={"GET"})
     */
    public function getByUser()
    {

    }
}