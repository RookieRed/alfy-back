<?php
/**
 * Created by PhpStorm.
 * User: celoundou-ngah
 * Date: 21/06/2018
 * Time: 17:17
 */

namespace App\Service;


use App\Entity\User;
use App\Enum\UserRoles;
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

    public function getById(int $id): ?User
    {
        return $this->userRepo->findOneBy([
            'id'    => $id
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
            $user->addRole(UserRoles::STUDENT);
        } else {
            $user->addRole($role);
        }

        $this->em->persist($user);
        return $this->jwtManager->create($user);
    }

}