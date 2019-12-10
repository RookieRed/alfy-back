<?php


namespace App\Service;


use App\Entity\Pojo\PageFilesIn;
use App\Repository\FileRepository;
use App\Repository\PageFileRepository;
use App\Repository\PageRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class PageService
{

    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var FileRepository
     */
    private $fileRepository;
    /**
     * @var PageRepository
     */
    private $pageRepository;
    /**
     * @var PageFileRepository
     */
    private $pageFileRepository;

    public function __construct(
        EntityManagerInterface $em,
        PageRepository $pageRepository,
        PageFileRepository $pageFileRepository,
        FileRepository $fileRepository
    ) {
        $this->em = $em;
        $this->pageRepository = $pageRepository;
        $this->pageFileRepository = $pageFileRepository;
        $this->fileRepository = $fileRepository;
    }

    public function updatePageFiles(PageFilesIn $pageBean)
    {
        $page = $this->pageRepository->findOneBy(['name' => $pageBean->getPageName()]);
        if ($page === null) {
            throw new \InvalidArgumentException('Page is not found');
        }

        foreach ($pageBean->getFilesInfo() as $pageFilePojo) {
            if ($pageFilePojo->getId() === null) {
                continue;
            }
            $pageFileEntity = $this->pageFileRepository->findByPageOrFile($page, $pageFilePojo->getId());
            $pageFileEntity->setOptions($pageFilePojo->getConfig());
        }
        $this->em->flush();
    }
}