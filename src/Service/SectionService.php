<?php


namespace App\Service;


use App\Entity\Section;
use App\Repository\FileRepository;
use App\Repository\PageFileRepository;
use App\Repository\PageRepository;
use App\Repository\SectionRepository;
use Doctrine\ORM\EntityManagerInterface;

class SectionService
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
    /**
     * @var SectionRepository
     */
    private $sectionRepository;

    public function __construct(
        EntityManagerInterface $em,
        PageRepository $pageRepository,
        PageFileRepository $pageFileRepository,
        FileRepository $fileRepository,
        SectionRepository $sectionRepository
    )
    {
        $this->em = $em;
        $this->pageRepository = $pageRepository;
        $this->pageFileRepository = $pageFileRepository;
        $this->fileRepository = $fileRepository;
        $this->sectionRepository = $sectionRepository;
    }

    public function findById($id)
    {
        return $this->sectionRepository->find($id);
    }

    public function createSection(Section $section)
    {
        $this->em->persist($section);
    }
}