<?php


namespace App\Service;


use App\Entity\FAQCategory;
use App\Entity\FAQSection;
use App\Entity\Page;
use App\Entity\QuestionAnswered;
use App\Entity\Section;
use App\Entity\User;
use App\Repository\FAQCategoryRepository;
use App\Repository\PageRepository;
use App\Repository\QuestionAnsweredRepository;
use App\Repository\SectionRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
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
    /**
     * @var QuestionAnsweredRepository
     */
    private $questionsRepository;
    /**
     * @var ValidationService
     */
    private $validator;

    public function __construct(EntityManagerInterface $em,
                                PageService $pageService,
                                QuestionAnsweredRepository $questionsRepository,
                                SectionService $sectionService,
                                FAQCategoryRepository $categoryRepository,
                                ValidationService $validator) {
        $this->em= $em;
        $this->pageService= $pageService;
        $this->questionsRepository = $questionsRepository;
        $this->sectionService= $sectionService;
        $this->categoryRepository= $categoryRepository;
        $this->validator = $validator;
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

    public function createCategory(FAQCategory &$categoryBean, User $user)
    {
        $this->validator->validateOrThrowException($categoryBean, ['create_faq_category']);

        if ($categoryBean->getSectionId() === null) {
            /** @var FAQSection $faqSection */
            $faqSection = $this->sectionService->findByCode('main-faq');
        } else {
            /** @var FAQSection $faqSection */
            $faqSection = $this->sectionService->findById($categoryBean->getSectionId());
        }

        if ($faqSection === null) {
            throw new BadRequestHttpException("FAQ Section object not found");
        }
        $faqSection->addCategory($categoryBean);
        $faqSection->setLastWriter($user);
        $faqSection->setUpdatedAt(new Date());
        $this->em->persist($categoryBean);
        $this->em->persist($faqSection);
        $this->em->flush();
        return $categoryBean;
    }

    public function findCategoryByIdOrException(int $categoryId): FAQCategory
    {
        $category = $this->categoryRepository->find($categoryId);
        if ($category === null) {
            throw new NotFoundHttpException("Category does not exist");
        }
        return $category;
    }

    public function findQuestionByIdOrException(int $questionId): QuestionAnswered
    {
        $question = $this->questionsRepository->find($questionId);
        if ($question === null) {
            throw new NotFoundHttpException("Question/answer does not exist");
        }
        return $question;
    }

    public function createQuestion(QuestionAnswered $questionBean, User $user)
    {
        $this->validator->validateOrThrowException($questionBean, ['create_question_answered']);

        $categoryId = $questionBean->getCategoryId();
        if ($categoryId === null) {
            throw new BadRequestHttpException("Category ID must be specified.");
        }
        $category = $this->findCategoryByIdOrException($categoryId);

        $category->getFaqSection()
            ->setUpdatedAt(new \DateTime())
            ->setLastWriter($user);
        $category->addQuestion($questionBean);
        $this->em->persist($category->getFaqSection());
        $this->em->flush();
        return $questionBean;
    }

    public function updateQuestion(QuestionAnswered $questionBean): QuestionAnswered
    {
        $this->validator->validateOrThrowException($questionBean, ['update_question_answered']);
        $questionFromDB = $this->findQuestionByIdOrException($questionBean->getId());

        $questionFromDB->setCategory($this->findCategoryByIdOrException($questionBean->getCategoryId()))
            ->setOrderIndex($questionBean->getOrderIndex())
            ->setQuestion($questionBean->getQuestion())
            ->setAnswer($questionBean->getAnswer());

        $this->em->persist($questionFromDB);
        $this->em->flush();
        return $questionFromDB;
    }

    public function deleteQuestion(int $id)
    {
        $question = $this->findQuestionByIdOrException($id);
        $this->em->remove($question);
        $this->em->flush();
    }

    public function deleteCategory(int $id)
    {
        $category = $this->findCategoryByIdOrException($id);
        $this->em->remove($category);
        $this->em->flush();
    }

    public function updateCategory(FAQCategory $categoryBean)
    {
        $this->validator->validateOrThrowException($categoryBean, ['update_faq_category']);
        $categoryFromDB = $this->findCategoryByIdOrException($categoryBean->getId());

        $categoryFromDB->setName($categoryBean->getName())
            ->setDescription($categoryBean->getDescription())
            ->setFaqSection($this->sectionService->findByIdOr404($categoryBean->getSectionId()))
            ->setOrderIndex($categoryBean->getOrderIndex());
        $this->em->persist($categoryFromDB);
        $this->em->flush();
        return $categoryBean;
    }

    public function setQuestionOrderIndex(int $questionId, int $newOrderIndex): Page
    {
        $question = $this->findQuestionByIdOrException($questionId);
        $category = $question->getCategory();
        $this->reorderCollection($category->getQuestions(), $question, $newOrderIndex);

        $this->validator->validateOrThrowException($category);
        $this->em->persist($category);
        $this->em->flush();
        return $this->pageService->findByLinkOrThrowException($category->getFaqSection()->getPage()->getLink());
    }

    public function setCategoryOrderIndex(int $categoryId, int $newOrderIndex): Page
    {
        $category = $this->findCategoryByIdOrException($categoryId);
        $section = $category->getFaqSection();

        $this->reorderCollection($section->getCategories(), $category, $newOrderIndex);
        $this->validator->validateOrThrowException($section);
        $this->em->persist($section);
        $this->em->flush();
        return $this->pageService->findByLinkOrThrowException($section->getPage()->getLink());
    }

    private function reorderCollection(Collection $collection, $element, int $newOrderIndex)
    {
        if ($newOrderIndex < 0) {
            $newOrderIndex = 0;
        } elseif ($newOrderIndex >= $collection->count()) {
            $newOrderIndex = $collection->count() - 1;
        }
        $previousOrderIndex = $element->getOrderIndex();

        foreach ($collection as &$currentElement) {
            if($previousOrderIndex < $newOrderIndex
                && ($currentElement->getOrderIndex() > $previousOrderIndex
                    &&  $currentElement->getOrderIndex() <= $newOrderIndex)) {
                $currentElement->setOrderIndex($currentElement->getOrderIndex() - 1);
            } elseif ($previousOrderIndex > $newOrderIndex
                && ($currentElement->getOrderIndex() < $previousOrderIndex
                    &&  $currentElement->getOrderIndex() >= $newOrderIndex)) {
                $currentElement->setOrderIndex($currentElement->getOrderIndex() + 1);
            }
        }
        $element->setOrderIndex($newOrderIndex);
    }


}