<?php

namespace App\DataFixtures;

use App\Service\LocationService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Finder\Finder;

class FromSQLFilesFixture extends Fixture implements ContainerAwareInterface
{
    const FIXTURES_DIR = 'config/fixtures';

    /**
     * @var ConsoleOutput
     */
    private $out;
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var LocationService
     */
    private $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->out = new ConsoleOutput();
        $this->locationService = $locationService;
    }

    public function load(ObjectManager $manager)
    {
        $finder = new Finder();
        $finder->files()->in(self::FIXTURES_DIR);

        $i = 0;
        foreach ($finder->files() as $file) {
            if (strstr($file->getFilename(), ".sql") === false) {
                continue;
            }
            $sql = $file->getContents();
            $manager->getConnection()
                ->prepare($sql)
                ->execute();
            ++$i;
        }
        $this->out->writeln("<info>$i fixtures files from config/fixtures have been loaded.</info>");

        // Set priority to countries
        try {
            $countriesUpdates = [];
            $countriesUpdates[] = $this->locationService->findCountryByCode('fr')->setPriority(10);
            $countriesUpdates[] = $this->locationService->findCountryByCode('cm')->setPriority(10);
            $countriesUpdates[] = $this->locationService->findCountryByCode('ca')->setPriority(9);
            $countriesUpdates[] = $this->locationService->findCountryByCode('de')->setPriority(8);
            $countriesUpdates[] = $this->locationService->findCountryByCode('es')->setPriority(8);
            $countriesUpdates[] = $this->locationService->findCountryByCode('cn')->setPriority(7);
            $countriesUpdates[] = $this->locationService->findCountryByCode('hk')->setPriority(6);
            $countriesUpdates[] = $this->locationService->findCountryByCode('pt')->setPriority(5);
            $countriesUpdates[] = $this->locationService->findCountryByCode('it')->setPriority(5);
            $countriesUpdates[] = $this->locationService->findCountryByCode('ru')->setPriority(5);
            $countriesUpdates[] = $this->locationService->findCountryByCode('sg')->setPriority(5);
            $countriesUpdates[] = $this->locationService->findCountryByCode('nl')->setPriority(4);
            $countriesUpdates[] = $this->locationService->findCountryByCode('au')->setPriority(4);
            $countriesUpdates[] = $this->locationService->findCountryByCode('nz')->setPriority(3);
            foreach ($countriesUpdates as $entity) {
                $manager->persist($entity);
            }
            $manager->flush();
        } catch (\Exception $e) {
            $this->out->writeln("<warning> /!\ Exception raised while updating country priority : </warning>");
            $this->out->writeln("<warning>$e</warning>");
        }
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
