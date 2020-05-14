<?php


namespace App\Service;


use App\Entity\HTMLSection;
use App\Entity\Section;
use App\Repository\FileRepository;
use App\Repository\PageRepository;
use App\Repository\SectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
     * @var SectionRepository
     */
    private $sectionRepository;
    /**
     * @var ValidationService
     */
    private $validationService;
    /**
     * @var UserService
     */
    private $userService;

    public function __construct(
        EntityManagerInterface $em,
        PageRepository $pageRepository,
        FileRepository $fileRepository,
        ValidationService $validationService,
        UserService $userService,
        SectionRepository $sectionRepository
    )
    {
        $this->em = $em;
        $this->pageRepository = $pageRepository;
        $this->fileRepository = $fileRepository;
        $this->sectionRepository = $sectionRepository;
        $this->validationService = $validationService;
        $this->userService = $userService;
    }

    public function findById(int $id)
    {
        return $this->sectionRepository->find($id);
    }

    public function findByCode(string $code)
    {
        return $this->sectionRepository->findBy(['code' => $code]);
    }

    public function findByIdOr404(int $id)
    {
        $section = $this->findById($id);
        if ($section == null) {
            throw new NotFoundHttpException("Page section not found.");
        }
        return $section;
    }

    public function createSection(Section $section)
    {
        $this->em->persist($section);
    }

    public function updateHtmlSection(HtmlSection $sectionBean)
    {
        // Find
        /** @var HTMLSection $sectionFromDb */
        $sectionFromDb = $this->findByIdOr404($sectionBean->getId());
        $currentUser = $this->userService->getConnectedUserOrThrowException();

        // Update
        $sectionFromDb->setHtml($sectionBean->getHtml())
            ->setUpdatedAt(new \DateTime());
        $beanTitle = $sectionBean->getTitle();
        if ($beanTitle === null && count($beanTitle) > 0) {
            $sectionFromDb->setTitle($beanTitle);
        }

        // Persist
        $this->validationService->validateOrThrowException($sectionFromDb, ['update_page_section']);
        $this->em->persist($sectionFromDb->getPage()->setUpdatedAt(new \DateTime()));
        $this->em->flush();
        return $sectionFromDb;
    }
}