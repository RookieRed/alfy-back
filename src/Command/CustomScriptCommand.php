<?php

namespace App\Command;

use App\Constants\FileConstants;
use App\Entity\SlideShowSection;
use App\Service\FileService;
use App\Service\PageService;
use App\Service\SectionService;
use App\Service\UserService;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CustomScriptCommand extends Command
{
    /** @var ObjectManager */
    private $manager;
    /** @var PageService */
    private $pageService;
    /** @var UserService */
    private $userService;
    /**@var SectionService */
    private $sectionService;
    /** @var ContainerInterface */
    private $container;
    /** @var FileService */
    private $fileService;

    protected static $defaultName = 'custom:script';

    private function __constructor(ObjectManager $manager, ContainerInterface $container)
    {
        $args = func_get_args();
        var_dump(count($args));
        var_dump($args);
        die;
        $this->manager = $manager;
        $this->container = $container;
        $this->userService = $container->get(UserService::class);
        $this->fileService = $container->get(FileService::class);
        $this->sectionService = $container->get(SectionService::class);
        $this->pageService = $container->get(PageService::class);

        parent::__construct(self::$defaultName);
    }

    protected function configure()
    {
        $this->setDescription('Commande custom à modifier à l\'infini sur la prod.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $io = new SymfonyStyle($input, $output);

        $this->userService = $this->container->get(UserService::class);
        $this->fileService = $this->container->get(FileService::class);
        $this->sectionService = $this->container->get(SectionService::class);
        $this->pageService = $this->container->get(PageService::class);

        try {
            $rookiered = $this->userService->findByUsernameOrThrowException('rookiered');
            $pageAbout = $this->pageService->findByLinkOrThrowException('/about');

            $diapoSection = new SlideShowSection();
            $diapoSection->setCreator($rookiered)
                ->setTitle('Diapositives accueil')
                ->setCreatedAt(new \DateTime())
                ->setOrderIndex(0)
                ->setCode('default');

            $diapoSection->addPhoto($this->fileService->findByPathOrThrowException(
                FileConstants::SLIDE_SHOW_DIR . 'default/alfy-meeting.jpg'
            ));
            $diapoSection->addPhoto($this->fileService->findByPathOrThrowException(
                FileConstants::SLIDE_SHOW_DIR . 'default/programme-ete.png'
            ));
            $diapoSection->addPhoto($this->fileService->findByPathOrThrowException(
                FileConstants::SLIDE_SHOW_DIR . 'default/anciens-voyagent.jpg'
            ));
            $diapoSection->addPhoto($this->fileService->findByPathOrThrowException(
                FileConstants::SLIDE_SHOW_DIR . 'default/fustel.jpg'
            ));
            $this->manager->persist($diapoSection);
            $pageAbout->addSection($diapoSection);

//            $this->manager->flush();
            $io->success('Commande exécutée');
            return 0;
        }
        catch (\Exception $e) {
            $io->error($e->getMessage());
            $io->error('Stack trace : ');
            $io->error($e->getTraceAsString());
            $io->error('Il y a eu une erreur.');

            return $e->getCode() ?? -1;
        }

    }
}
