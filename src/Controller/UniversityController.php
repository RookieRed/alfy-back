<?php
/**
 * Created by PhpStorm.
 * User: celoundou-ngah
 * Date: 25/06/2018
 * Time: 13:43
 */

namespace App\Controller;

use App\Utils\JsonSerializer;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as Doc;

/**
 * @Route(path="/university")
 */
class UniversityController extends JsonAbstractController
{
    public function __construct(JsonSerializer $serializer)
    {
        parent::__construct($serializer);
    }

    /**
     * @Route(path="/all",
     *     name="university_get_all",
     *     methods={"GET"})
     * @Doc\Tag(name="Universités", description="Gestion des universités et écoles supérieures.")
     * @Doc\Response(response=200, description="[A CHANGER] OK")
     */
    public function getAll()
    {

    }

    /**
     * @Route(path="/{id}",
     *     name="university_get",
     *     methods={"GET"},
     *     requirements={"id"="\d+"})
     * @Doc\Tag(name="Universités", description="Gestion des universités et écoles supérieures.")
     * @Doc\Response(response=200, description="[A CHANGER] OK")
     */
    public function getById()
    {

    }

    /**
     * @Route(path="",
     *     name="university_add",
     *     methods={"PUT"})
     * @Doc\Tag(name="Universités", description="Gestion des universités et écoles supérieures.")
     * @Doc\Response(response=200, description="[A CHANGER] OK")
     */
    public function create()
    {

    }

    /**
     * @Route(path="/{id}",
     *     name="university_update",
     *     methods={"POST"},
     *     requirements={"id"="\d+"})
     * @Doc\Tag(name="Universités", description="Gestion des universités et écoles supérieures.")
     * @Doc\Response(response=200, description="[A CHANGER] OK")
     */
    public function update()
    {

    }

    /**
     * @Route(path="/{id}",
     *     methods={"DELETE"},
     *     name="university_delete",
     *     requirements={"id"="\d+"})
     * @Doc\Tag(name="Universités", description="Gestion des universités et écoles supérieures.")
     * @Doc\Response(response=200, description="[A CHANGER] OK")
     */
    public function remove()
    {

    }

    /**
     * @Route(path="",
     *     name="university_search",
     *     methods={"GET"})
     * @Doc\Tag(name="Universités", description="Gestion des universités et écoles supérieures.")
     * @Doc\Response(response=200, description="[A CHANGER] OK")
     */
    public function search()
    {

    }
}