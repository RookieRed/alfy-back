<?php

namespace App\DataFixtures;

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

    public function __construct()
    {
        $this->out = new ConsoleOutput();
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
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
