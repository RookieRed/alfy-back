<?php
/**
 * Created by PhpStorm.
 * User: celoundou-ngah
 * Date: 21/06/2018
 * Time: 14:20
 */

namespace App\Controller;


use App\Constants\UserRoles;
use App\Entity\FAQCategory;
use App\Entity\QuestionAnswered;
use App\Service\FAQService;
use App\Service\UserService;
use App\Service\ValidationService;
use App\Utils\JsonSerializer;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as Doc;

/**
 * Class CareerController
 * @package App\Controller
 */
class FAQController extends JsonAbstractController
{
    /** @var FAQService */
    private $faqService;
    /** @var ValidationService */
    private $validator;
    /** @var UserService */
    private $userService;

    public function __construct(JsonSerializer $serializer,
                                FAQService $faqService,
                                UserService $userService) {
        parent::__construct($serializer);
        $this->faqService = $faqService;
        $this->userService = $userService;
    }

    /**
     * @Route(path="/pages/faq/categories", name="get_faq_categories_list", methods={"GET"})
     * @Doc\Tag(name="FAQ", description="Gestion des questions fréquentes")
     * @Doc\Response(response=200, description="Liste des catégories disponibles.",
     *     @Model(type=App\Entity\FAQCategory::class, groups={"get_categories_list"}))
     * @Doc\Response(response=204, description="Pas de catégories.")
     */
    public function getAllCategories()
    {
        $categories = $this->faqService->findAllCategoriesFromPage("/faq");

        if (count($categories) === 0) {
            return $this->noContent();
        }
        return $this->jsonOK($categories, ['get_categories_list']);
    }

    /**
     * @Route(path="/faq/categories", name="create_new_category", methods={"PUT"})
     * @Doc\Tag(name="FAQ", description="Gestion des questions fréquentes")
     * @Doc\Parameter(description="Nouvelle catégorie", in="body", required=true, name="category",
     *     @Model(type=App\Entity\FAQCategory::class, groups={"create_faq_category"})))
     * @Doc\Response(response=400, description="Erreur dans la requête.")
     * @Doc\Response(response=401, description="Non authorisé.")
     * @Doc\Response(response=201, description="Enregistré.",
     *     @Model(type=App\Entity\FAQCategory::class, groups={"get_page"}))
     */
    public function addCategory(Request $request)
    {
        // Check rights
        $user = $this->userService->checkConnectedUserPrivilegedOrThrowException(
            UserRoles::ADMIN, "You must be administrator to do that.");

        /** @var FAQCategory $categoryBean */
        $categoryBean = $this->serializer->jsonDeserialize(
            $request->getContent(),
            FAQCategory::class,
            ['create_faq_category']
        );
        $category = $this->faqService->createCategory($categoryBean, $user);

        return $this->json($category, Response::HTTP_CREATED, ["get_page"]);
    }

    /**
     * @Route(path="/faq/categories", name="update_faq_category", methods={"POST"})
     * @Doc\Tag(name="FAQ", description="Gestion des questions fréquentes")
     * @Doc\Parameter(description="Catégorie", in="body", required=true, name="category",
     *     @Model(type=App\Entity\FAQCategory::class, groups={"update_faq_category"})))
     * @Doc\Response(response=200, description="Modifications enregistrées.",
     *     @Model(type=App\Entity\FAQCategory::class, groups={"get_page"}) )
     * @Doc\Response(response=400, description="Erreur dans la requête.")
     * @Doc\Response(response=401, description="Non authorisé.")
     * @Doc\Response(response=404, description="Catégorie non trouvée.")
     */
    public function updateCategory(Request $request)
    {
        // Check rights
        $user = $this->userService->checkConnectedUserPrivilegedOrThrowException(
            UserRoles::ADMIN, "You must be administrator to do that.");

        /** @var FAQCategory $categoryBean */
        $categoryBean = $this->serializer->jsonDeserialize(
            $request->getContent(),
            FAQCategory::class,
            ['update_faq_category']
        );

        return $this->jsonOK($this->faqService->updateCategory($categoryBean), ['get_page']);
    }

    /**
     * @Route(path="/faq/categories/{categoriyId}", name="delete_faq_category",
     *     methods={"DELETE"}, requirements={"categoriyId" = "\d+"})
     * @Doc\Tag(name="FAQ", description="Gestion des questions fréquentes")
     * @Doc\Parameter(description="ID de la catégorie à supprimer", in="path", required=true,
     *     name="categoriyId", type="integer")
     * @Doc\Response(response=204, description="Question supprimée")
     * @Doc\Response(response=404, description="Question non trouvée")
     * @Doc\Response(response=401, description="Non authorisé")
     */
    public function deleteCategory(Request $request)
    {
        // Check rights
        $this->userService->checkConnectedUserPrivilegedOrThrowException(
            UserRoles::ADMIN, "You must be administrator to do that.");
        $this->faqService->deleteCategory(+$request->get("categoriyId"));

        return $this->noContent();
    }

    /**
     * @Route(path="/faq/questions", name="create_question_answered", methods={"PUT"})
     * @Doc\Tag(name="FAQ", description="Gestion des questions fréquentes")
     * @Doc\Parameter(description="Nouvelle question", in="body", required=true, name="question",
     *     @Model(type=App\Entity\QuestionAnswered::class, groups={"create_question_answered"})))
     * @Doc\Response(response=201, description="Question-Réponse ajoutée.",
     *     @Model(type=App\Entity\QuestionAnswered::class, groups={"get_page"})))
     * @Doc\Response(response=400, description="Erreur dans la requête.")
     * @Doc\Response(response=401, description="Non authorisé.")
     * @Doc\Response(response=404, description="Catégorie non trouvée.")
     */
    public function addQuestion(Request $request)
    {
        // Check rights
        $user = $this->userService->checkConnectedUserPrivilegedOrThrowException(
            UserRoles::ADMIN, "You must be administrator to do that.");
         // Check params
        /** @var QuestionAnswered $questionBean */
        $questionBean = $this->serializer->jsonDeserialize(
            $request->getContent(),
            QuestionAnswered::class,
            ['create_question_answered']
        );

        return $this->json($this->faqService->createQuestion($questionBean, $user),
            Response::HTTP_CREATED, ['get_page']);
    }

    /**
     * @Route(path="/faq/questions", name="update_question_answered", methods={"POST"})
     * @Doc\Tag(name="FAQ", description="Gestion des questions fréquentes")
     * @Doc\Parameter(description="Question", in="body", required=true, name="question",
     *     @Model(type=App\Entity\QuestionAnswered::class, groups={"update_question_answered"})))
     * @Doc\Response(response=200, description="Modifications enregistrées.",
     *     @Model(type=App\Entity\QuestionAnswered::class, groups={"get_page"}) )
     * @Doc\Response(response=400, description="Erreur dans la requête.")
     * @Doc\Response(response=401, description="Non authorisé.")
     * @Doc\Response(response=404, description="Question non trouvée.")
     */
    public function updateQuestion(Request $request)
    {
        // Check rights
        $user = $this->userService->checkConnectedUserPrivilegedOrThrowException(
            UserRoles::ADMIN, "You must be administrator to do that.");

        // Check param
        /** @var QuestionAnswered $questionBean */
        $questionBean = $this->serializer->jsonDeserialize(
            $request->getContent(),
            QuestionAnswered::class,
            ['update_question_answered']
        );

        return $this->jsonOK($this->faqService->updateQuestion($questionBean), ['get_page']);
    }

    /**
     * @Route(path="/faq/questions/{questionId}", requirements={"questionId" = "\d+"},
     *     name="delete_question_answered", methods={"DELETE"})
     * @Doc\Tag(name="FAQ", description="Gestion des questions fréquentes")
     * @Doc\Parameter(description="ID Question à supprimer", in="path", required=true,
     *     name="questionId", type="integer")
     * @Doc\Response(response=204, description="Question supprimée")
     * @Doc\Response(response=404, description="Question non trouvée")
     * @Doc\Response(response=401, description="Non authorisé")
     */
    public function deleteQuestion(Request $request)
    {
        // Check rights
        $this->userService->checkConnectedUserPrivilegedOrThrowException(
            UserRoles::ADMIN, "You must be administrator to do that.");
        $this->faqService->deleteQuestion(+$request->get("questionId"));

        return $this->noContent();
    }

    /**
     * @Route(path="/faq/categories/{categoryId}/re-order/{newOrderIndex}",
     *     requirements={"questionId" = "\d+", "newOrderIndex" = "\d+"},
     *     name="set_category_order_index", methods={"POST"})
     * @Doc\Tag(name="FAQ", description="Gestion des questions fréquentes")
     * @Doc\Parameter(description="ID Catégory à ré-ordonner", in="path", required=true,
     *     name="categoryId", type="integer")
     * @Doc\Parameter(description="Nouvel index", in="path", required=true,
     *     name="newOrderIndex", type="integer")
     * @Doc\Response(response=200, description="Nouvel ordre enregistré",
     *     @Model(type=App\Entity\Page::class, groups={"get_page"}))
     * @Doc\Response(response=404, description="Catégorie non trouvée")
     * @Doc\Response(response=401, description="Non authorisé")
     */
    public function setCategoryOrderIndex(Request $request)
    {
        // Check rights
        $this->userService->checkConnectedUserPrivilegedOrThrowException(
            UserRoles::ADMIN, "You must be administrator to do that.");

        $categoryId = +$request->get('categoryId');
        $newOrderIndex = +$request->get('newOrderIndex');
        $page = $this->faqService->setCategoryOrderIndex($categoryId, $newOrderIndex);

        return $this->jsonOK($page, ['get_page']);
    }

    /**
     * @Route(path="/faq/questions/{questionId}/re-order/{newOrderIndex}",
     *     requirements={"questionId" = "\d+", "newOrderIndex" = "\d+"},
     *     name="set_question_order_index", methods={"POST"})
     * @Doc\Tag(name="FAQ", description="Gestion des questions fréquentes")
     * @Doc\Parameter(description="ID Question à ré-ordonner", in="path", required=true,
     *     name="questionId", type="integer")
     * @Doc\Parameter(description="Nouvel index", in="path", required=true,
     *     name="newOrderIndex", type="integer")
     * @Doc\Response(response=404, description="Question non trouvée")
     * @Doc\Response(response=401, description="Non authorisé")
     */
    public function setQuestionOrderIndex(Request $request)
    {
        // Check rights
        $this->userService->checkConnectedUserPrivilegedOrThrowException(
            UserRoles::ADMIN, "You must be administrator to do that.");

        $questionId = +$request->get('questionId');
        $newOrderIndex = +$request->get('newOrderIndex');
        $page = $this->faqService->setQuestionOrderIndex($questionId, $newOrderIndex);

        return $this->jsonOK($page, ['get_page']);
    }
}