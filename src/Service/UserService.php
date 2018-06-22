<?php
/**
 * Created by PhpStorm.
 * User: celoundou-ngah
 * Date: 21/06/2018
 * Time: 17:17
 */

namespace App\Service;


use App\Entity\Pojo\UserConnectionIn;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

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

    public function __construct(EntityManagerInterface $em,
                                UserPasswordEncoderInterface $encoder,
                                JWTTokenManagerInterface $jwtManager,
                                UserRepository $userRepository)
    {
        $this->em = $em;
        $this->userRepo = $userRepository;
        $this->encoder = $encoder;
        $this->jwtManager = $jwtManager;
    }

    public function checkCredentials(Request $request, UserConnectionIn $user): ?string
    {
        $userFromDB = $this->userRepo->findOneBy([
            'username' => $user->getUsername(),
        ]);
        if ($userFromDB == null || $userFromDB->getSalt() == null || $userFromDB->getPassword() == null) {
            throw new \Exception('User does not exist');
        }

        $encryptedPassword = $this->encoder->encodePassword($userFromDB, $user->getPassword());
        if ($encryptedPassword === $userFromDB->getPassword()) {
            return $this->jwtManager->create($userFromDB);
        }
        return null;
    }

    public function getById(int $id)
    {

    }

    public function getConnectedUser()
    {
        //return $this->jwtManager->
    }

}