<?php
/**
 * Created by PhpStorm.
 * User: celoundou-ngah
 * Date: 21/06/2018
 * Time: 13:38
 */

namespace App\Controller;

use App\Constants\FileConstants;
use App\Constants\UserRoles;
use App\Entity\User;
use App\Service\FileService;
use App\Service\UserService;
use App\Service\ValidationService;
use App\Utils\JsonSerializer;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class AccountController
 * @package App\Controller
 *
 * @Route(path="/account")
 */
class AccountController extends BaseJsonController
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
     * @var FileService
     */
    private $fileService;

    /**
     * AccountController constructor.
     * @param UserService $userService
     * @param EntityManagerInterface $em
     * @param JsonSerializer $serializer
     * @param FileService $fileService
     * @param ValidationService $validator
     */
    public function __construct(UserService $userService,
                                EntityManagerInterface $em,
                                JsonSerializer $serializer,
                                FileService $fileService,
                                ValidationService $validator)
    {
        parent::__construct($serializer);
        $this->userService = $userService;
        $this->validator = $validator;
        $this->em = $em;
        $this->fileService = $fileService;
    }

    /**
     * @Route(path="/me",
     *     methods={"GET"},
     *     name="account_get_mine"
     * )
     */
    public function getMine()
    {
        $user = $this->userService->getConnectedUserOrThrowException();
        return $this->jsonOK($user, ['user_get']);
    }

    /**
     * @Route(path="/{id}",
     *     methods={"GET"},
     *     name="account_get_by_id",
     *     requirements={"id"="\d+"}
     * )
     */
    public function getById(Request $request)
    {
        $userId = $request->get('id');
        if ($userId == null) {
            throw new BadRequestHttpException('No id provided');
        }

        $user = $this->userService->getById($userId);
        if ($user == null) {
            throw new NotFoundHttpException('User not find');
        }

        $connectedUser = $this->userService->getConnectedUser();
        if ($connectedUser != null && $connectedUser->getId() == $userId) {
            $groups = ['user_get'];
        } else {
            $groups = ['user_get_list'];
        }
        return $this->jsonOK($user, $groups);
    }

    /**
     * @Route(path="/me",
     *     methods={"POST"},
     *     name="account_update_mine"
     * )
     */
    public function updateMine(Request $request)
    {
        /** @var User $userBean */
        $userBean = $this->serializer->jsonDeserialize($request->getContent(), User::class, ['user_update']);
        $this->validator->validateOrThrowException($userBean, ['user_update']);

        $updatedUser = $this->userService->updateConnectedUser($userBean);
        $this->em->flush();
        return $this->jsonOK($updatedUser, ['user_get']);
    }

    /**
     * @Route(path="/signin",
     *     methods={"POST"},
     *     name="signin"
     * )
     */
    public function singIn()
    {
    }

    /**
     * @Route(path="/signup",
     *     methods={"PUT"},
     *     name="account_create"
     * )
     */
    public function singUp(Request $request)
    {
        /** @var User $userBean */
        $userBean = $this->serializer->jsonDeserialize(
            $request->getContent(), User::class, ['account_create']);
        $userBean->setBirthDay(new DateTime($userBean->getBirthDay()));
        $this->validator->validateOrThrowException($userBean, ['account_create']);

        $jwtToken = $this->userService->createAccount($userBean);
        $this->em->flush();
        return $this->json(['token' => $jwtToken], Response::HTTP_CREATED);
    }


    /**
     * @Route(path="/login/{username}",
     *     methods={"GET"},
     *     name="check_username"
     * )
     */
    public function checkUsernameValidity(string $username)
    {
        // Vérification de l'unicité
        if ($this->userService->usernameExists($username)) {
            return $this->json('Username exists', Response::HTTP_CONFLICT);
        } else {
            return $this->noContent();
        }
    }

    /**
     * @Route(path="/email/{email}",
     *     methods={"GET"},
     *     name="check_email"
     * )
     */
    public function checkEmailValidity(string $email)
    {
        // Vérification de l'unicité
        if ($this->userService->emailExists($email)) {
            return $this->json('Email exists', Response::HTTP_CONFLICT);
        } else {
            return $this->noContent();
        }
    }

    /**
     * @Route(path="/{who}",
     *     methods={"DELETE"},
     *     name="account_delete",
     *     requirements={"who"="\d+|me"}
     * )
     */
    public function delete(Request $request)
    {
        $connectedUser = $this->userService->getConnectedUserOrThrowException();
        $userId = $request->get('who');
        if ($userId === 'me') {
            $target = $connectedUser;

        } else {
            if (!$connectedUser->isRole(UserRoles::ADMIN)) {
                throw new AccessDeniedException('You can not delete an other account');
            }
            $target = $this->userService->getById($userId);
            if ($target == null) {
                throw new NotFoundHttpException('User account does not exists');
            }
        }
        $this->userService->deletePermanentlyUser($target);
        return $this->noContent();
    }

    /**
     * @Route(path="/pictures",
     *     methods={"POST"},
     *     name="update_profile_picture"
     * )
     */
    public function updateProfilePicture(Request $request)
    {
        $user = $this->userService->getConnectedUserOrThrowException();

        /** @var File $file */
        $file = $request->files->get('picture');
        if ($file == null) {
            throw new BadRequestHttpException('No picture found');
        }

        $picture = $this->fileService->saveFile($file, $user,
            FileConstants::PROFILE_PICTURES_DIR . $user->getUsername() . '/');
        $user->setProfilePicture($picture);
        $this->em->flush();
        return $this->jsonOK($picture, ['user_get']);
    }
}