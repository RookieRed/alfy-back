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
use App\Service\FAQService;
use App\Service\UserService;
use App\Service\ValidationService;
use App\Utils\JsonSerializer;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
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
                                UserService $userService,
                                ValidationService $validator) {
        parent::__construct($serializer);
        $this->faqService = $faqService;
        $this->userService = $userService;
        $this->validator = $validator;
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
     * @Doc\Response(response=403, description="Non authorisé.")
     * @Doc\Response(response=204, description="Enregistré.",
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
        $this->validator->validateOrThrowException($categoryBean, ['create_faq_category']);

        $category = $this->faqService->createCategory($categoryBean, $user);
        return $this->jsonOK($category, ["get_page"]);
    }

    /*
     * @Route(path="/categories", name="", methods={""})
     * @Doc\Tag(name="FAQ", description="Gestion des questions fréquentes")
     * @Doc\Response(response=200, description="[A CHANGER] OK")
     */
    public function updateCategory() {}

    /*
     * @Route(path="/categories", name="", methods={""})
     * @Doc\Tag(name="FAQ", description="Gestion des questions fréquentes")
     * @Doc\Response(response=200, description="[A CHANGER] OK")
     */
    public function removeCategory() {}

    /*
     * @Route(path="/categories", name="", methods={""})
     * @Doc\Tag(name="FAQ", description="Gestion des questions fréquentes")
     * @Doc\Response(response=200, description="[A CHANGER] OK")
     */
    public function removeQuestion() {}

    /*
     * @Route(path="/categories", name="", methods={""})
     * @Doc\Tag(name="FAQ", description="Gestion des questions fréquentes")
     * @Doc\Response(response=200, description="[A CHANGER] OK")
     */
    public function addQuestion() {}

    /*
     * @Route(path="/categories", name="", methods={""})
     * @Doc\Tag(name="FAQ", description="Gestion des questions fréquentes")
     * @Doc\Response(response=200, description="[A CHANGER] OK")
     */
    public function updateQuestion() {}

}