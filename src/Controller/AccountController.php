<?php
/**
 * Created by PhpStorm.
 * User: celoundou-ngah
 * Date: 21/06/2018
 * Time: 13:38
 */

namespace App\Controller;

use App\Entity\Pojo\UserConnectionIn;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 * Class AccountController
 * @package App\Controller
 *
 * @Route(path="/account")
 */
class AccountController extends Controller
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * AccountController constructor.
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @Route(path="/me",
     *     methods={"GET"},
     *     name="account_get"
     * )
     */
    public function getMine()
    {

    }

    /**
     * @Route(path="/{id}",
     *     methods={"GET"},
     *     name="account_get",
     *     requirements={"id"="\d+"}
     * )
     * @param Request $request
     */
    public function getById(Request $request)
    {

    }

    /**
     * @Route(path="/me",
     *     methods={"POST"},
     *     name="account_update"
     * )
     */
    public function updateMine()
    {

    }

    /**
     * @Route(path="/signin",
     *     methods={"POST"},
     *     name="signin"
     * )
     *
     * @param UserConnectionIn $user
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function singIn(UserConnectionIn $user)
    {
        try {
            $jwtToken = $this->userService->checkCredentials($user);
        } catch (\Exception $e) {
            return $this->json($e->getMessage(), Response::HTTP_NOT_FOUND);
        }

        if ($jwtToken === null) {
            return $this->json('Incorrect password', Response::HTTP_BAD_REQUEST);
        }
        return $this->json(['token' => $jwtToken]);
    }

    /**
     * @Route(path="/signup",
     *     methods={"PUT"},
     *     name="account_create"
     * )
     */
    public function singUp()
    {

    }

    /**
     * @Route(path="/signout",
     *     methods={"GET"},
     *     name="signout"
     * )
     */
    public function singOut()
    {

    }

    /**
     * @Route(path="/me",
     *     methods={"DELETE"},
     *     name="account_delete"
     * )
     */
    public function removeMine()
    {

    }
}