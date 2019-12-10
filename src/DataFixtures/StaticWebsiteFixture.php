<?php

namespace App\DataFixtures;

use App\Constants\FileConstants;
use App\Entity\HTMLSection;
use App\Entity\Page;
use App\Entity\File;
use App\Entity\EventTile;
use App\Entity\TilesEventsSection;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\ORMException;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class StaticWebsiteFixture extends Fixture implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /** @var File[] */
    private $files;

    /** @var ValidatorInterface */
    private $validator;

    public function load(ObjectManager $manager)
    {
        $this->validator = $this->container->get("validator");
        try {
            $this->createFiles($manager);
            $this->createPages($manager);
        } catch (ORMException $e) {
            $out = new ConsoleOutput();
            $out->writeln("<error>An exception occurred while importing data :</error>");
            $out->writeln($e->getTrace());
            $manager->rollback();
        }
    }

    /**
     * @inheritDoc
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    private function createPages(ObjectManager $manager) {
        $manager->beginTransaction();

        // About page
        $aboutPage = new Page();
        $aboutPage->setLink("/about")
            ->setName("about")
            ->addSection(
                (new HTMLSection())
                    ->setTitle("Qui sommes-nous ?")
                    ->setCode("intro")
                    ->setHtml("<img class=\"logo-alfy\" align=\"right\" src=\"/assets/img/logo-alfy.jpg\"/>
      <p>L’association ALFY des <b>Anciens du Lycée Français de Yaoundé</b> rassemble toute personne de plus de 16 ans ayant
        été scolarisée au moins un an au Lycée Fustel de Coulanges de Yaoundé, tout membre du personnel administratif
        comme professoral et enfin tout parent d'élève. </p>
      <h4>C’est de la pensée que découle l’action.</h4>
      <p>L'ALFY est <b>née en septembre 2016</b> pensée dans le but premier d'aider la promotion de bacheliers dans leur orientation
        post-bac, en les mettant en contact avec des anciens élèves dont la formation scolaire et professionnelle s'inscrirait
        dans le cadre de leurs ambitions. Suite à une réflexion menée avec la proviseur du lycée en fonction en 2017, l'ALFY
        est <b>passée du statut de projet à celui d'association</b> dont la vie a été rythmée depuis par une collaboration restreinte
        avec le <a href=\"https://www.fustel-yaounde.net/\">lycée Fustel de Coulanges</a>, <a href=\"https://alfm.fr/\">l'association mondiale ALFM</a>, l'AEFE et d'autres associations locales telles que
        l'ALFD du lycée français de Dakar et la tenue de nombreuses rencontres sur différents sites géographiques. </p>")
            )
            ->addSection((new TilesEventsSection())
                ->setTitle("Nos sponsors")
                ->setCode("sponsors")
                ->addTile((new EventTile())
                    ->setTitle("Sponsor A")
                    ->setLink("https://www.google.com/")
                    ->setDescription("Notre tout premier sponsor")
                    ->setPhoto($this->files[FileConstants::DEFAULT_NO_IMAGE_FILE])
                )
                ->addTile((new EventTile())
                    ->setTitle("Association B")
                    ->setDescription("Une association qui nous a toujours soutenu")
                    ->setPhoto($this->files[FileConstants::DEFAULT_NO_IMAGE_FILE])
                )
                ->addTile((new EventTile())
                    ->setTitle("Sponsor C")
                    ->setDescription("Un troisième sponsor qui nous a accompagné")
                    ->setPhoto($this->files[FileConstants::DEFAULT_NO_IMAGE_FILE])
                )
            )
            ->addSection((new TilesEventsSection())
                ->setTitle("Agenda")
                ->setCode("agenda")
                ->addTile((new EventTile())
                    ->setTitle("Prochain évènement")
                    ->setDescription("Un troisième sponsor qui nous a accompagné")
                    ->setPhoto($this->files[FileConstants::DEFAULT_NO_IMAGE_FILE])
                    ->setDate(new \DateTime("2019-12-25"))
                )
            );
        assert(count($this->validator->validate($aboutPage)) == 0, "About page entity is not valid.");

        // High school page
        $highSchoolPage = new Page();
        $highSchoolPage->setName("Le lycée")
            ->setLink("/school");
        assert(count($this->validator->validate($highSchoolPage)) == 0, "High school page entity is not valid.");

        // TODO Association page
        $assoPage = new Page();
        $assoPage->setName("L'association'")
            ->setLink("/association")
            ->addSection((new TilesEventsSection())
                ->setTitle("Evenements importants")
                ->setCode("asso-timeline")
                ->addTile((new EventTile())
                    ->setTitle("Création de l'association")
                    ->setDate(new \DateTime("2015-06-01"))
                )
                ->addTile((new EventTile())
                    ->setTitle("Création de l'association")
                    ->setDate(new \DateTime("2015-08-15"))
                )
            )
            ->addSection((new HTMLSection())
                ->setTitle("Petit mot sur l'association")
                ->setCode("asso")
                ->setHtml("<p>Présentation de l&amp;association</p>")
            );
        assert(count($this->validator->validate($assoPage)) == 0, "Association page entity is not valid.");

        $manager->persist($aboutPage);
        $manager->persist($highSchoolPage);
        $manager->persist($assoPage);
        $this->commit($manager);
    }

    private function createFiles(ObjectManager $manager) {
        $manager->beginTransaction();
        $this->scanFiles();
        $nbFiles = count($this->files);
        // Print results
        $out = new ConsoleOutput();
        $out->writeln("<info>Fixture has found $nbFiles files object to flush.</info>");
        foreach ($this->files as $file) {
            $manager->persist($file);
        }
        $this->commit($manager);
    }

    private function scanFiles() {
        $rootDir = $this->container->getParameter('kernel.project_dir') . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR;
        $symfonyFS = $this->container->get("filesystem");

        $getDirsRecursive = function($recFunc, $dir, &$results) use ($symfonyFS, $rootDir) {
            $files = scandir($dir);
            foreach($files as $key => $value) {
                $absolutePath = realpath($dir) . DIRECTORY_SEPARATOR . $value;
                if(!is_dir($absolutePath)) {
                    $file = new File();
                    $file->setName(basename($absolutePath))
                        ->setPath(dirname(DIRECTORY_SEPARATOR
                            . $symfonyFS->makePathRelative($absolutePath, $rootDir)) . DIRECTORY_SEPARATOR);
                    assert(count($this->validator->validate($file)) == 0, "File entity is not valid.");
                    $results[$file->getFullPath()] = $file;
                } else if($value != "." && $value != "..") {
                    $recFunc($recFunc, $absolutePath, $results);
                }
            }

            return $results;
        };
        $results = [];
        $this->files = $getDirsRecursive($getDirsRecursive, $rootDir . 'files' . DIRECTORY_SEPARATOR, $results);
    }

    /**
     * @param ObjectManager $manager
     */
    private function commit(ObjectManager $manager): void
    {
        $manager->flush();
        $manager->getConnection()->commit();
    }
}
