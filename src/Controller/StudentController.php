<?php
/**
 * Created by PhpStorm.
 * User: celoundou-ngah
 * Date: 25/06/2018
 * Time: 14:15
 */

namespace App\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class StudentController
 * @package App\Controller
 *
 * @Route(path="/students")
 */
class StudentController extends Controller
{

    public function __construct()
    {
    }

    /**
     * @Route(path="",
     *     methods={"GET"},
     *     name="user_search")
     */
    public function search()
    {

    }

    /**
     * @Route(path="/import",
     *     methods={"PUT"},
     *     name="user_import"
     * )
     */
    public function importFromExcel()
    {

    }

    /**
     * @Route(path="",
     *     methods={"PUT"},
     *     name="user_add"
     * )
     */
    public function addUser()
    {

    }

}