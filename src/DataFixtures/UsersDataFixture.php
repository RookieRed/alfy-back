<?php

namespace App\DataFixtures;

use App\Constants\FileConstants;
use App\Constants\UserRoles;
use App\Entity\Project;
use App\Entity\Study;
use App\Entity\University;
use App\Entity\User;
use App\Service\FileService;
use App\Service\UserService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
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
        try {
            // Create ADMIN universities
            $paulSab = new University();
            $paulSab->setName("Université Paul Sabatier - Toulouse 3");
            $creteilUniv = new University();
            $creteilUniv->setName("Université Paris-Est Créteil (UPEC)");
            $parisSud = new University();
            $parisSud->setName("Université Paris-Sud");
            $ecolePont = new University();
            $ecolePont->setName("École des Ponts ParisTech");
            $manager->persist($paulSab);
            $manager->persist($creteilUniv);
            $manager->persist($parisSud);
            $manager->persist($ecolePont);
            $manager->flush();

            // Create ADMIN studies
            $iutStudy = new Study();
            $iutStudy->setStartedAt(new \DateTime("2014-09-01"))
                ->setType("IUT")
                ->setMonthsDuration(24)
                ->setComment("Meilleure expérience ever.")
                ->setNotation(5)
                ->setUniversity($paulSab);
            $masterCed = new Study();
            $masterCed->setStartedAt(new \DateTime("2014-09-01"))
                ->setType("Licence - Master")
                ->setMonthsDuration(36)
                ->setComment("La fac ça craint un peu...")
                ->setNotation(3)
                ->setUniversity($paulSab);

            $masterMGK1 = new Study();
            $masterMGK1->setType("Licence - Master")
                ->setUniversity($parisSud)
                ->setMonthsDuration(48)
                ->setStartedAt(new \DateTime("2014-09-01"));
            $masterMGK2 = new Study();
            $masterMGK2->setType("Master 1")
                ->setUniversity($creteilUniv)
                ->setMonthsDuration(12)
                ->setStartedAt(new \DateTime("2018-09-01"));
            $masterMGK3 = new Study();
            $masterMGK3->setType("Master 2")
                ->setUniversity($ecolePont)
                ->setMonthsDuration(12)
                ->setStartedAt(new \DateTime("2019-09-01"));

            // Create ADMIN careers
            $alfyProjet = new Project();
            $alfyProjet->setTitle("Association du Lycée Français de Yaooundé")
                ->setStartedAt(new \DateTime("2016-11-01"))
                ->setDetails("Création de l'association des anciens du lycée français de Yaoundé.");
            $alfyWebSite = new Project();
            $alfyWebSite->setTitle("Site internet d'ALFY")
                ->setStartedAt(new \DateTime("2018-07-01"))
                ->setDetails("Création du site internet d'ALFY");

            // Create ADMIN account
            $cedric = new User();
            $cedric->setUsername("rookiered")
                ->setFirstName("Cédric")
                ->setLastName("Eloundou")
                ->setEmail("cedric@rookie.red")
                ->setPassword("toB3changed")
                ->setJobTitle("Alternant M2 DL - VISEO")
                ->setBirthDay(new \DateTime("1996-06-13"))
                ->setBaccalaureate("Scientifique")
                ->setLinkedIn("https://www.linkedin.com/in/c%C3%A9dric-eloundou-181a05105/")
                ->setFacebook("https://www.facebook.com/cedric.eloundou3")
                ->setPersonalWebsite("https://www.rookie.red/")
                ->setInstagram("https://www.instagram.com/celoundou/")
                ->setPhone("+33 6 12 34 65 78");
            $cedric->addProject($alfyWebSite);
            $cedric->addStudy($iutStudy);
            $cedric->addStudy($masterCed);
            $this->userService->createAccount($cedric, UserRoles::ADMIN, false);

            $mgk = new User();
            $mgk->setUsername("mg.kouamedho")
                ->setFirstName("Marie Gabrielle")
                ->setLastName("Kouamedjo")
                ->setEmail("knt.mariegabrielle@gmail.com")
                ->setJobTitle("Présidente de ALFY")
                ->setBirthDay(new \DateTime("25-02-1997"))
                ->setBaccalaureate("Scientifique")
                ->setPassword("toB3changed")
                ->setLinkedIn("https://www.linkedin.com/in/mariegabrielle-kouamedjo/")
                ->setFacebook("https://www.facebook.com/knt.mariegabrielle")
                ->setTwitter("https://twitter.com/____MGK____")
                ->setPhone("+33 6 12 34 65 78");
            $mgk->addProject($alfyProjet);
            $mgk->addStudy($masterMGK1);
            $mgk->addStudy($masterMGK2);
            $mgk->addStudy($masterMGK3);
            $this->userService->createAccount($mgk, UserRoles::ADMIN, false);

            $manager->flush();
            $this->out->writeln("<info>Add 2 admin accounts to database.</info>");

            // Create users from import
            $report = $this->fileService->importFromExcel($this->container->getParameter('kernel.project_dir') .
                FileConstants::DEFAULT_CONTACT_CSV_FILE);
            $nbErrors = $report->getNbErrors();
            $this->out->writeln("<info>Import finished with $nbErrors errors. You can check out the report in the database table.</info>");
            $manager->flush();
        } catch (\Exception $e) {
            $this->out->writeln($e->getMessage());
            $this->out->writeln();
            $this->out->writeln($e->getTraceAsString());
            throw $e;
        }
    }
}