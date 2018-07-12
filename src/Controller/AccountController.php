<?php
/**
 * Created by PhpStorm.
 * User: celoundou-ngah
 * Date: 21/06/2018
 * Time: 13:38
 */

namespace App\Controller;

use App\Entity\Pojo\UserConnectionIn;
use App\Entity\User;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * AccountController constructor.
     * @param UserService $userService
     * @param EntityManagerInterface $em
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     */
    public function __construct(UserService $userService,
                                EntityManagerInterface $em,
                                SerializerInterface $serializer,
                                ValidatorInterface $validator)
    {
        $this->userService = $userService;
        $this->validator = $validator;
        $this->em = $em;
        $this->serializer = $serializer;
    }

    /**
     * @Route(path="/me",
     *     methods={"GET"},
     *     name="account_get_mine"
     * )
     */
    public function getMine()
    {
        $user = $this->userService->getConnectedUser();
        if ($user === null) {
            throw new \Exception('How do you do this ?');
        }

        $jsonResponse = new Response($this->serializer->serialize($user, 'json', ['groups' => ['user_get']]));
        $jsonResponse->headers->set('Content-type', 'application/json');
        return $jsonResponse;
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
        $userId = $request->get('id');
        if ($userId == null){
            return $this->json('No id provided', Response::HTTP_BAD_REQUEST);
        }

        $user = $this->userService->getById($userId);
        if ($user == null) {
            return $this->json('User not find', Response::HTTP_NOT_FOUND);
        }

        if ($this->userService->getConnectedUser()->getId() == $userId) {
            $options = ['groups' => ['user_get']];
        } else {
            $options = ['groups' => ['user_get_list']];
        }

        $jsonResponse = new Response($this->serializer->serialize($user, 'json', $options));
        $jsonResponse->headers->set('Content-type', 'application/json');
        return $jsonResponse;
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
     */
    public function singIn() { }

    /**
     * @Route(path="/signup",
     *     methods={"PUT"},
     *     name="account_create"
     * )
     */
    public function singUp(Request $request)
    {
        $userBean = $this->serializer->deserialize($request->getContent(),
            User::class, 'json', ['groups' => ['account_create']]);
        $userBean->setBirthDay(new \DateTime($userBean->getBirthDay()));

        $errors = $this->validator->validate($userBean, null, 'account_create');
        if ($errors->count() > 0) { // TODO Validation du pojo
            throw new ValidatorException('Pojo');
        }

        $jwtToken = $this->userService->createAccount($userBean);
        if ($jwtToken === null) {
            return $this->json('Error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $this->em->flush();
        return $this->json(['token' => $jwtToken], Response::HTTP_CREATED);
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