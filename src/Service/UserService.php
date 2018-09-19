<?php
/**
 * Created by PhpStorm.
 * User: celoundou-ngah
 * Date: 21/06/2018
 * Time: 17:17
 */

namespace App\Service;


use App\Entity\Address;
use App\Entity\File;
use App\Entity\User;
use App\Constants\UserRoles;
use App\Repository\CountryRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\JWTUserToken;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWSProvider\JWSProviderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\TokenExtractorInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

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
    private $countryRepository;

    public function __construct(EntityManagerInterface $em,
                                UserPasswordEncoderInterface $encoder,
                                JWTTokenManagerInterface $jwtManager,
                                TokenStorageInterface $tokenStorage,
                                CountryRepository $countryRepository,
                                UserRepository $userRepository)
    {
        $this->em = $em;
        $this->userRepo = $userRepository;
        $this->countryRepository = $countryRepository;
        $this->encoder = $encoder;
        $this->jwtManager = $jwtManager;
        $this->tokenStorage = $tokenStorage;
        $this->fileSystem = new Filesystem();
    }

    /**
     * @return User[]
     */
    public function getAll() {
        return $this->userRepo->findBy([], ['lastName' => 'ASC','firstName' => 'ASC']);
    }

    public function getById(int $id): ?User
    {
        return $this->userRepo->findOneBy([
            'id'    => $id
        ]);
    }

    /**
     * @param User $userSearched
     * @return User[]
     */
    public function searchByCriteria(User $userSearched): array {
        $whereClause = '';
        $params = [];
        if ($userSearched->getFirstName() != null) {
            $whereClause .= ' AND u.firstName LIKE :firstName';
            $params['firstName'] = '%'.$userSearched->getFirstName().'%';
        }
        if ($userSearched->getLastName() != null) {
            $whereClause .= ' AND u.lastName LIKE :lastName';
            $params['lastName'] = '%'.$userSearched->getLastName().'%';
        }
        if ($userSearched->getUsername() != null) {
            $whereClause .= ' AND u.username LIKE :username';
            $params['username'] = '%'.$userSearched->getUsername().'%';
        }
        if ($userSearched->getEmail() != null) {
            $whereClause .= ' AND u.email LIKE :email';
            $params['email'] = '%'.$userSearched->getEmail().'%';
        }
        if ($userSearched->getBirthDay() != null) {
            $birthYear = $userSearched->getBirthDay();
            if ($birthYear instanceof \DateTimeInterface) {
                $birthYear = $birthYear->format('Y');
            }
            $whereClause .= ' AND YEAR(u.birthDay) = :birthYear';
            $params['birthYear'] = $birthYear;
        }

        if (strlen($whereClause) == 0) {
            return $this->getAll();
        }

        $whereClause = substr($whereClause, 4, strlen($whereClause));
        return $this->em
            ->createQuery('SELECT u FROM App\Entity\User u WHERE '.$whereClause.' ORDER BY u.lastName, u.firstName')
            ->setParameters($params)
            ->execute();
    }

    /**
     * @param string $name
     * @return User[]
     */
    public function searchByName(?string $name): array {
        if ($name == null || trim($name) == '') {
            return $this->getAll();
        }

        $namesArray = explode(' ', $name);
        $whereClause = '';
        $params = [];
        $i = 0;
        foreach ($namesArray as $val) {
            $key = 'name' . $i++;
            $whereClause .= " AND (u.username LIKE :$key OR u.firstName LIKE :$key OR u.lastName LIKE :$key)";
            $params[$key] = '%'.$val.'%';
        }

        $whereClause = substr($whereClause, 4, strlen($whereClause));
        return $this->em
            ->createQuery('SELECT u FROM App\Entity\User u WHERE '.$whereClause.' ORDER BY u.lastName, u.firstName')
            ->setParameters($params)
            ->execute();
    }

    public function usernameExists(string $username): bool {
        return null != $this->userRepo->findOneBy([
            'username' => $username
        ]);
    }

    public function emailExists(string $email): bool {
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

    public function createAccount(User $user, ?string $role=null): ?string
    {
        $encryptedPassword = $this->encoder->encodePassword($user, $user->getPassword());
        $user->setPassword($encryptedPassword);

        if ($role === null || ($role !== UserRoles::STUDENT && $role !== UserRoles::ADMIN && $role !== UserRoles::SPONSOR)) {
            $user->setRole(UserRoles::STUDENT);
        } else {
            $user->setRole($role);
        }

        $this->em->persist($user);
        return $this->jwtManager->create($user);
    }

    public function updateConnectedUser(User $userBean): User
    {
        $target = $this->getConnectedUser();

        $target->setUsername($userBean->getUsername());
        $target->setFirstName($userBean->getFirstName());
        $target->setLastName($userBean->getLastName());
        $target->setBirthDay($userBean->getBirthDay());
        $target->setPhone($userBean->getPhone());
        $target->setFacebook($userBean->getFacebook());
        $target->setLinkedIn($userBean->getLinkedIn());
        $target->setTwitter($userBean->getTwitter());

        $newAddress = $userBean->getAddress();
        if ($newAddress != null) {

            $countryId = $newAddress->getCountryId();
            if ($countryId === null) {
                throw new Exception('No country id specified');
            } else {
                $country = $this->countryRepository->findOneBy(['id' => $countryId]);
                if ($country == null) {
                    throw new Exception('The specified country does not exist');
                }
            }

            $actualAddress = $target->getAddress();
            if ($actualAddress == null) {
                $actualAddress = new Address();
            }
            $actualAddress->setLine1($newAddress->getLine1());
            $actualAddress->setLine2($newAddress->getLine2());
            $actualAddress->setCity($newAddress->getCity());
            $actualAddress->setCountry($country);

            $target->setAddress($actualAddress);
            $this->em->persist($actualAddress);
        }

        if ($userBean->getPassword() != null) {
            // TODO strong passwords
            $target->setPassword($this->encoder->encodePassword($userBean, $userBean->getPassword()));
        }

        return $target;
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

}