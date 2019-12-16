<?php
/**
 * Created by PhpStorm.
 * User: celoundou-ngah
 * Date: 21/06/2018
 * Time: 14:20
 */

namespace App\Controller;


use App\Utils\JsonSerializer;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as Doc;

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
     * @Route(path="/{careerId}", name="career_update", methods={"PUT"})
     * @Doc\Tag(name="Carrières", description="Gestion des carrières des utilisateurs.")
     * @Doc\Response(response=200, description="[A CHANGER] OK")
     */
    public function update()
    {

    }

    /**
     * @Route(path="", name="career_create", methods={"POST"})
     * @Doc\Tag(name="Carrières", description="Gestion des carrières des utilisateurs.")
     * @Doc\Response(response=200, description="[A CHANGER] OK")
     */
    public function create()
    {

    }

    /**
     * @Route(path="/{careerId}", name="career_remove", methods={"DELETE"})
     * @Doc\Tag(name="Carrières", description="Gestion des carrières des utilisateurs.")
     * @Doc\Response(response=200, description="[A CHANGER] OK")
     */
    public function remove()
    {

    }

    /**
     * @Route(path="/{careerId}", name="career_by_id", methods={"GET"})
     * @Doc\Tag(name="Carrières", description="Gestion des carrières des utilisateurs.")
     * @Doc\Response(response=200, description="[A CHANGER] OK")
     */
    public function getById()
    {

    }

    /**
     * @Route(path="/user/{userId}", name="career_by_user", methods={"GET"})
     * @Doc\Tag(name="Carrières", description="Gestion des carrières des utilisateurs.")
     * @Doc\Response(response=200, description="[A CHANGER] OK")
     */
    public function getByUserId()
    {

    }
}