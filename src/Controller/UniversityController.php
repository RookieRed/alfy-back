<?php
/**
 * Created by PhpStorm.
 * User: celoundou-ngah
 * Date: 25/06/2018
 * Time: 13:43
 */

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/university")
 */
class UniversityController extends Controller
{

    public function __construct()
    {
    }

    /**
     * @Route(path="/all",
     *     name="university_get_all",
     *     methods={"GET"})
     */
    public function getAll()
    {

    }
    /**
     * @Route(path="/{id}",
     *     name="university_get",
     *     methods={"GET"},
     *     requirements={"id"="\d+"})
     */
    public function getById()
    {

    }

    /**
     * @Route(path="",
     *     name="university_add",
     *     methods={"PUT"})
     */
    public function addOne()
    {

    }

    /**
     * @Route(path="/{id}",
     *     name="university_update",
     *     methods={"POST"},
     *     requirements={"id"="\d+"})
     */
    public function update()
    {

    }

    /**
     * @Route(path="/{id}",
     *     methods={"DELETE"},
     *     name="university_delete",
     *     requirements={"id"="\d+"})
     */
    public function remove()
    {

    }

    /**
     * @Route(path="",
     *     name="university_search",
     *     methods={"GET"})
     */
    public function search()
    {

    }
}