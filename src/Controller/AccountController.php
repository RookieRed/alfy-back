<?php
/**
 * Created by PhpStorm.
 * User: celoundou-ngah
 * Date: 21/06/2018
 * Time: 13:38
 */

namespace App\Controller;

use App\Constants\ErrorType;
use App\Constants\FileConstants;
use App\Entity\User;
use App\Service\FileService;
use App\Service\UserService;
use App\Service\ValidationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidatorException;

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
     * @var ValidationService
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
     * @var FileService
     */
    private $fileService;

    /**
     * AccountController constructor.
     * @param UserService $userService
     * @param EntityManagerInterface $em
     * @param SerializerInterface $serializer
     * @param ValidationService $validator
     */
    public function __construct(UserService $userService,
                                EntityManagerInterface $em,
                                SerializerInterface $serializer,
                                FileService $fileService,
                                ValidationService $validator)
    {
        $this->userService = $userService;
        $this->validator = $validator;
        $this->em = $em;
        $this->fileService = $fileService;
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
            return $this->validator->generateErrorResponse(
                ErrorType::EXCEPTION['code'],
                ErrorType::EXCEPTION['message']
            );
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
    public function updateMine(Request $request)
    {
        /** @var User $userBean */
        $userBean = $this->serializer->deserialize($request->getContent(),
            User::class, 'json', ['groups' => ['user_update']]);
        $userBean->setBirthDay(new \DateTime($userBean->getBirthDay()));
        $errors = $this->validator->validateBean($userBean, ['user_update']);
        if ($errors != null) {
            return $errors;
        }

        $updatedUser = $this->userService->updateConnectedUser($userBean);
        $this->em->flush();

        $json = $this->serializer->serialize($updatedUser, 'json', ['groups' => ['user_get']]);
        return new JsonResponse($json, Response::HTTP_NO_CONTENT, [], true);
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

        $errorResponse = $this->validator->validateBean($userBean, ['account_create']);
        if ($errorResponse !== null) {
           return $errorResponse;
        }

        $jwtToken = $this->userService->createAccount($userBean);
        if ($jwtToken === null) {
            return $this->json('Error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $this->em->flush();
        return $this->json(['token' => $jwtToken], Response::HTTP_CREATED);
    }


    /**
     * @Route(path="/login/{username}",
     *     methods={"GET"},
     *     name="check_username"
     * )
     * @param string $login
     */
    public function checkUsernameValidity(string $username) {
        $response = new \stdClass();
        $response->valid = true;
        $response->message = "OK";

        // Vérification de l'unicité
        if($this->userService->usernameExists($username)) {
            $response->valid = false;
            $response->message = "Username already exists.";
        }

        return $this->json($response);
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

    /**
     * @Route(path="/pictures",
     *     methods={"POST"},
     *     name="update_profile_picture"
     * )
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function updateProfilePicture(Request $request)
    {
        $user = $this->userService->getConnectedUser();

        /** @var \Symfony\Component\HttpFoundation\File\File $file */
        $file = $request->files->get('picture');
        if ($file == null) {
            return $this->json('No picture found', Response::HTTP_BAD_REQUEST);
        }

        $picture = $this->fileService->saveFile($file, $user,
            FileConstants::PROFILE_PICTURES_DIR . $user->getUsername() . '/');
        $user->setProfilePicture($picture);
        $this->em->flush();

        $json = $this->serializer->serialize($picture, 'json', ['groups' => ['user_get']]);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }
}