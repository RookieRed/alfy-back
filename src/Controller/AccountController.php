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
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Swagger\Annotations as Doc;

/**
 * Class AccountController
 * @package App\Controller
 *
 * @Route(path="/account")
 */
class AccountController extends JsonAbstractController
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
     * @Doc\Tag(name="Comptes utilisateur", description="Gestion des connections / inscriptions / comptes utilisateur.")
     * @Doc\Response(response=200, description="Tout va bien.",
     *     @Model(type=App\Entity\User::class, groups={"user_get"}))
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
     * @Doc\Tag(name="Comptes utilisateur", description="Gestion des connections / inscriptions / comptes utilisateur.")
     * @Doc\Response(response=200, description="Retourne l'utilisateur demandé",
     *     @Model(type=App\Entity\User::class, groups={"user_get"}))
     */
    public function getById(Request $request)
    {
        $userId = $request->get('id');
        if ($userId == null) {
            throw new BadRequestHttpException('No id provided');
        }

        $user = $this->userService->findById($userId);
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
     * @Doc\Tag(name="Comptes utilisateur", description="Gestion des connections / inscriptions / comptes utilisateur.")
     * @Doc\Response(response=200, description="Utilisateur modifié avec succès.",
     *     @Model(type=App\Entity\User::class, groups={"user_get"}))
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
     * @Doc\Tag(name="Comptes utilisateur", description="Gestion des connections / inscriptions / comptes utilisateur.")
     * @Doc\Parameter(in="body", required=true, description="Identifiants de l'utilisateur.", name="credentials",
     *      @Model(type=App\Entity\User::class, groups={"user_connect"}))
     * @Doc\Response(response=200, description="URL pour la connexion de l'utilisateur.",
     *     @Doc\Schema(type="object",
     *          @Doc\Property(type="string", description="Jetton d'authenfication", property="token")
     *     )
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
     * @Doc\Tag(name="Comptes utilisateur", description="Gestion des connections / inscriptions / comptes utilisateur.")
     * @Doc\Response(response=200, description="Utilisateur modifié avec succès.",
     *     @Doc\Schema(type="object",
     *          @Doc\Property(type="string", description="Jetton d'authenfication", property="token")
     *     )
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
     * @Doc\Tag(name="Comptes utilisateur", description="Gestion des connections / inscriptions / comptes utilisateur.")
     * @Doc\Response(response=409, description="Le username existe déjà dans la base de données.")
     * @Doc\Response(response=204, description="Le nom d'utilisateur est disponible.")
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
     * @Doc\Tag(name="Comptes utilisateur", description="Gestion des connections / inscriptions / comptes utilisateur.")
     * @Doc\Response(response=409, description="L'adresse mail existe déjà dans la base de données.")
     * @Doc\Response(response=204, description="L'adresse mail est disponible.")
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
     * @Route(path="/{userIdOrMe}",
     *     methods={"DELETE"},
     *     name="account_delete",
     *     requirements={"userIdOrMe"="\d+|me"}
     * )
     * @Doc\Tag(name="Comptes utilisateur", description="Gestion des connections / inscriptions / comptes utilisateur.")
     * @Doc\Parameter(name="userIdOrMe", type="string",
     *     description="Cible à supprimer, peut être un ID ou le string 'me'", required=true, in="path")
     * @Doc\Response(response=404, description="L'utilisateur à supprimer n'existe pas")
     * @Doc\Response(response=204, description="Succès.")
     */
    public function delete(Request $request)
    {
        $connectedUser = $this->userService->getConnectedUserOrThrowException();
        $userId = $request->get('userIdOrMe');
        if ($userId === 'me') {
            $target = $connectedUser;

        } else {
            if (!$connectedUser->isRole(UserRoles::ADMIN)) {
                throw new AccessDeniedException('You can not delete an other account');
            }
            $target = $this->userService->findById($userId);
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
     * @Doc\Tag(name="Comptes utilisateur", description="Gestion des connections / inscriptions / comptes utilisateur.")
     * @Doc\Response(response=400, description="Le fichier image 'est pas trouvée ou invalide")
     * @Doc\Response(response=200, description="Photo de profil enregistré",
     *     @Model(type=App\Entity\File::class, groups={"user_get", "user_get_list"}))
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