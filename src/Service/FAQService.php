<?php


namespace App\Service;


use App\Entity\FAQSection;
use App\Entity\Section;
use App\Repository\FAQCategoryRepository;
use App\Repository\PageRepository;
use App\Repository\SectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Collection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FAQService
{

    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var PageService
     */
    private $pageService;
    /**
     * @var FAQCategoryRepository
     */
    private $categoryRepository;
    /**
     * @var SectionService
     */
    private $sectionService;

    public function __construct(
        EntityManagerInterface $em,
        PageService $pageService,
        SectionService $sectionService,
        FAQCategoryRepository $categoryRepository
    ) {
        $this->em= $em;
        $this->pageService= $pageService;
        $this->sectionService= $sectionService;
        $this->categoryRepository= $categoryRepository;
    }

    /**
     * @return FAQCategory[]
     */
    public function findAllCategoriesFromPage()
    {
        /** @var FAQSection $faqSection */
        $faqSection = $this->sectionService->findByCode('main-faq');
        if ($faqSection === null) {
            throw  new NotFoundHttpException("Page or section not found.");
        }
        return $this->categoryRepository->findBy(['faqSection' => $faqSection]);
    }


}