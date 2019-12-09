<?php

namespace App\DataFixtures;

use App\Constants\FileConstants;
use App\Constants\UserRoles;
use App\Entity\File;
use App\Entity\User;
use App\Service\FileService;
use App\Service\UserService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UsersDataFixture extends Fixture implements ContainerAwareInterface
{

    /**
     * @var ConsoleOutput
     */
    private $out;
    /**
     * @var ContainerInterface
     */
    private $container;

    private $fileService;
    /**
     * @var UserService
     */
    private $userService;

    public function __construct(FileService $fileService, UserService $userService)
    {
        $this->fileService = $fileService;
        $this->userService = $userService;
        $this->out = new ConsoleOutput();
    }

    /**
     * @inheritDoc
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        // Create users from import
        $report = $this->fileService->importFromExcel($this->container->getParameter('kernel.project_dir') .
            FileConstants::DEFAULT_CONTACT_CSV_FILE);
        $nbErrors = $report->getNbErrors();
        $this->out->writeln("<info>Import finished with $nbErrors errors. You can check out the report in the database table.</info>");
        $manager->flush();

        // Create ADMIN account
        $cedric = new User();
        $cedric->setUsername("rookiered")
            ->setFirstName("Cédric")
            ->setLastName("Eloundou")
            ->setEmail("cedric@rookie.red")
            ->setPassword("tobechanged")
            ->setRole(UserRoles::ADMIN);
        $this->userService->createAccount($cedric, UserRoles::ADMIN, false);
        $mgk = new User();
        $mgk->setUsername("mg.kouamedho")
            ->setFirstName("Marie Gabrielle")
            ->setLastName("Kouamedjo")
            ->setEmail("knt.mariegabrielle@gmail.com")
            ->setPassword("tobechanged");
        $this->userService->createAccount($mgk, UserRoles::ADMIN, false);

        $manager->flush();
        $this->out->writeln("<info>Add 2 admin accounts to database.</info>");
    }
}