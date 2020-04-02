<?php
/**
 * Created by PhpStorm.
 * User: celoundou-ngah
 * Date: 21/06/2018
 * Time: 17:17
 */

namespace App\Service;


use App\Constants\UserRoles;
use App\Entity\Address;
use App\Entity\File;
use App\Entity\User;
use App\Repository\CountryRepository;
use App\Repository\UserRepository;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var UserRepository
     */
    private $userRepo;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;
    /**
     * @var JWTTokenManagerInterface
     */
    private $jwtManager;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    /**
     * @var Filesystem
     */
    private $fileSystem;
    /**
     * @var CountryRepository
     */
    private $locationService;

    /**
     * @var ValidationService
     */
    private $validationService;

    const ACCENTED_CHAR_MAP = array(    'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
        'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
        'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
        'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
        'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );

    public function __construct(EntityManagerInterface $em,
                                UserPasswordEncoderInterface $encoder,
                                JWTTokenManagerInterface $jwtManager,
                                TokenStorageInterface $tokenStorage,
                                LocationService $locationService,
                                ValidationService $validationService,
                                UserRepository $userRepository)
    {
        $this->em = $em;
        $this->userRepo = $userRepository;
        $this->locationService = $locationService;
        $this->encoder = $encoder;
        $this->jwtManager = $jwtManager;
        $this->validationService = $validationService;
        $this->tokenStorage = $tokenStorage;
        $this->fileSystem = new Filesystem();
    }

    /**
     * @return User[]
     */
    public function findAll()
    {
        return $this->userRepo->findBy([], ['lastName' => 'ASC', 'firstName' => 'ASC']);
    }

    public function findById(int $id): ?User
    {
        return $this->userRepo->findOneBy([
            'id' => $id
        ]);
    }

    /**
     * @param User $userSearched
     * @return User[]
     */
    public function findByCriteria(User $userSearched): array
    {
        $whereClause = '';
        $params = [];
        if ($userSearched->getFirstName() != null) {
            $whereClause .= ' AND u.firstName LIKE :firstName';
            $params['firstName'] = '%' . $userSearched->getFirstName() . '%';
        }
        if ($userSearched->getLastName() != null) {
            $whereClause .= ' AND u.lastName LIKE :lastName';
            $params['lastName'] = '%' . $userSearched->getLastName() . '%';
        }
        if ($userSearched->getUsername() != null) {
            $whereClause .= ' AND u.username LIKE :username';
            $params['username'] = '%' . $userSearched->getUsername() . '%';
        }
        if ($userSearched->getEmail() != null) {
            $whereClause .= ' AND u.email LIKE :email';
            $params['email'] = '%' . $userSearched->getEmail() . '%';
        }
        if ($userSearched->getBirthDay() != null) {
            $birthYear = $userSearched->getBirthDay();
            if ($birthYear instanceof DateTimeInterface) {
                $birthYear = $birthYear->format('Y');
            }
            $whereClause .= ' AND YEAR(u.birthDay) = :birthYear';
            $params['birthYear'] = $birthYear;
        }

        if (strlen($whereClause) == 0) {
            return $this->findAll();
        }

        $whereClause = substr($whereClause, 4, strlen($whereClause));
        return $this->em
            ->createQuery('SELECT u FROM App\Entity\User u WHERE ' . $whereClause . ' ORDER BY u.lastName, u.firstName')
            ->setParameters($params)
            ->execute();
    }

    /**
     * @param string $name
     * @return User[]
     */
    public function findByName(?string $name): array
    {
        if ($name == null || trim($name) == '') {
            return $this->findAll();
        }

        $namesArray = explode(' ', $name);
        $whereClause = '';
        $params = [];
        $i = 0;
        foreach ($namesArray as $val) {
            $key = 'name' . $i++;
            $whereClause .= " AND (u.username LIKE :$key OR u.firstName LIKE :$key OR u.lastName LIKE :$key)";
            $params[$key] = '%' . $val . '%';
        }

        $whereClause = substr($whereClause, 4, strlen($whereClause));
        return $this->em
            ->createQuery('SELECT u FROM App\Entity\User u WHERE ' . $whereClause . ' ORDER BY u.lastName, u.firstName')
            ->setParameters($params)
            ->execute();
    }

    public function usernameExists(string $username): bool
    {
        $userMatch = $this->userRepo->findOneBy([
            'username' => $username
        ]);
        if ($userMatch == null) {
            return false;
        }
        return true;
    }

    public function emailExists(string $email): bool
    {
        return null != $this->userRepo->findOneBy([
                'email' => $email
            ]);
    }

    public function getConnectedUser(): ?User
    {
        $token = $this->tokenStorage->getToken();
        $userArray = $this->jwtManager->decode($token);
        return $this->userRepo->findOneBy(['username' => $userArray['username']]);
    }

    public function getConnectedUserOrThrowException($message = null): User
    {
        $user = $this->getConnectedUser();
        if ($user === null) {
            throw new UnauthorizedHttpException($message);
        }
        return $user;
    }

    public function checkConnectedUserPrivilegedOrThrowException(string $role, $message = null): User
    {
        $user = $this->getConnectedUserOrThrowException($message);
        if (!$user->isRole($role)) {
            throw new AccessDeniedHttpException($message);
        }
        return $user;
    }

    public function createAccount(User $user, ?string $role = null, $createJWT = true)
    {
        $this->validationService->validateOrThrowException($user, ["account_create"]);
        $encryptedPassword = $this->encoder->encodePassword($user, $user->getPassword());
        $user->setPassword($encryptedPassword);

        if ($role === null || ($role !== UserRoles::STUDENT && $role !== UserRoles::ADMIN && $role !== UserRoles::SPONSOR)) {
            $user->setRole(UserRoles::STUDENT);
        } else {
            $user->setRole($role);
        }

        $this->em->persist($user);
        if ($createJWT) {
            return $this->jwtManager->create($this->unaccentUsername($user));
        }
    }

    public function updateConnectedUser(User $userBean): User
    {
        $target = $this->getConnectedUser();

        $target->setUsername($userBean->getUsername());
        $target->setFirstName($userBean->getFirstName());
        $target->setLastName($userBean->getLastName());
        $target->setBirthDay($userBean->getBirthDay());
        $target->setPhone($userBean->getPhone());

        $facebook = $userBean->getFacebook();
        if ($facebook != null) {
            $path = parse_url($facebook, PHP_URL_PATH);
            $target->setFacebook($path != false ? $path : null);
            unset($path);
        }
        $linkedIn = $userBean->getLinkedIn();
        if ($linkedIn != null) {
            $path = parse_url($linkedIn, PHP_URL_PATH);
            $target->setLinkedIn($path != false ? $path : null);
            unset($path);
        }
        $twitter = $userBean->getTwitter();
        if ($twitter != null) {
            $path = parse_url($twitter, PHP_URL_PATH);
            $target->setTwitter($path != false ? $path : null);
            unset($path);
        }

        $newAddress = $userBean->getAddress();
        if ($newAddress != null) {

            $countryId = $newAddress->getCountryId();
            if ($countryId === null) {
                throw new BadRequestHttpException('No country id specified');
            } else {
                $country = $this->locationService->findOneBy(['id' => $countryId]);
                if ($country == null) {
                    throw new BadRequestHttpException('The specified country does not exist');
                }
            }

            $actualAddress = $target->getAddress();
            if ($actualAddress == null) {
                $actualAddress = new Address();
            }
            $actualAddress->setLine1($newAddress->getLine1());
            $actualAddress->setCity($newAddress->getCity());

            $target->setAddress($actualAddress);
            $this->em->persist($actualAddress);
        } else if ($target->getAddress() != null) {
            $this->em->remove($target->getAddress());
            $target->setAddress(null);
        }

        if ($userBean->getPassword() != null) {
            // TODO strong passwords
            $target->setPassword($this->encoder->encodePassword($userBean, $userBean->getPassword()));
        }

        return $this->unaccentUsername($target);
    }

    public function deletePermanentlyUser(User $target)
    {
        // Remove all files
        /** @var File[] $files */
        $files = $this->em->getRepository(File::class)->findBy([
            'owner' => $target
        ]);
        foreach ($files as $file) {
            $this->fileSystem->remove($file->getFullPath());
            $this->em->remove($file);
        }
        // Remove address
        $address = $target->getAddress();
        if ($address) {
            $this->em->remove($address);
        }
        // Remove user
        $this->em->remove($target);
        $this->em->flush();
    }

    public function unaccentUsername(User $user): User
    {
        return $user->setUsername(strtr($user->getUsername(), self::ACCENTED_CHAR_MAP));
    }

}