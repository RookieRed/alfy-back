<?php


namespace App\Service;


use App\Entity\Pojo\PageFilesIn;
use App\Repository\FileRepository;
use App\Repository\PageRepository;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;

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

    public function __construct(
        EntityManagerInterface $em,
        PageRepository $pageRepository,
        FileRepository $fileRepository
    )
    {
        $this->em = $em;
        $this->pageRepository = $pageRepository;
        $this->fileRepository = $fileRepository;
    }

    public function updatePageFiles(PageFilesIn $pageBean)
    {
//        $page = $this->pageRepository->findOneBy(['name' => $pageBean->getPageName()]);
//        if ($page === null) {
//            throw new InvalidArgumentException('Page is not found');
//        }
//
//        foreach ($pageBean->getFilesInfo() as $pageFilePojo) {
//            if ($pageFilePojo->getId() === null) {
//                continue;
//            }
//            $pageFileEntity = $this->pageFileRepository->findByPageOrFile($page, $pageFilePojo->getId());
//            $pageFileEntity->setOptions($pageFilePojo->getConfig());
//        }
//        $this->em->flush();
        // TODO
    }
}