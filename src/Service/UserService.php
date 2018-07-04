<?php
/**
 * Created by PhpStorm.
 * User: celoundou-ngah
 * Date: 21/06/2018
 * Time: 17:17
 */

namespace App\Service;


use App\Entity\User;
use App\Constants\UserRoles;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\JWTUserToken;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWSProvider\JWSProviderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\TokenExtractorInterface;
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

    public function __construct(EntityManagerInterface $em,
                                UserPasswordEncoderInterface $encoder,
                                JWTTokenManagerInterface $jwtManager,
                                TokenStorageInterface $tokenStorage,
                                UserRepository $userRepository)
    {
        $this->em = $em;
        $this->userRepo = $userRepository;
        $this->encoder = $encoder;
        $this->jwtManager = $jwtManager;
        $this->tokenStorage = $tokenStorage;
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

}