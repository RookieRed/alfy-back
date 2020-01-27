<?php
/**
 * Created by PhpStorm.
 * User: celoundou-ngah
 * Date: 21/06/2018
 * Time: 14:20
 */

namespace App\Controller;


use App\Repository\FAQCategoryRepository;
use App\Service\FAQService;
use App\Utils\JsonSerializer;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as Doc;

/**
 * Class CareerController
 * @package App\Controller
 *
 * @Route(path="/pages/faq")
 */
class FAQController extends JsonAbstractController
{
    /** @var FAQService */
    private $faqService;

    public function __construct(
        JsonSerializer $serializer,
        FAQService $faqService
    ) {
        parent::__construct($serializer);
        $this->faqService = $faqService;
    }

    /**
     * @Route(path="/categories", name="get_faq_categories_list", methods={"GET"})
     * @Doc\Tag(name="FAQ", description="Gestion des questions fréquentes")
     * @Doc\Response(response=200, description="Liste des catégories disponibles.")
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
     * @Route(path="/categories", name="", methods={""})
     * @Doc\Tag(name="FAQ", description="Gestion des questions fréquentes")
     * @Doc\Response(response=200, description="[A CHANGER] OK")
     */
    public function addCategory() {}

    /**
     * @Route(path="/categories", name="", methods={""})
     * @Doc\Tag(name="FAQ", description="Gestion des questions fréquentes")
     * @Doc\Response(response=200, description="[A CHANGER] OK")
     */
    public function updateCategory() {}

    /**
     * @Route(path="/categories", name="", methods={""})
     * @Doc\Tag(name="FAQ", description="Gestion des questions fréquentes")
     * @Doc\Response(response=200, description="[A CHANGER] OK")
     */
    public function removeCategory() {}

    /**
     * @Route(path="/categories", name="", methods={""})
     * @Doc\Tag(name="FAQ", description="Gestion des questions fréquentes")
     * @Doc\Response(response=200, description="[A CHANGER] OK")
     */
    public function removeQuestion() {}

    /**
     * @Route(path="/categories", name="", methods={""})
     * @Doc\Tag(name="FAQ", description="Gestion des questions fréquentes")
     * @Doc\Response(response=200, description="[A CHANGER] OK")
     */
    public function addQuestion() {}

    /**
     * @Route(path="/categories", name="", methods={""})
     * @Doc\Tag(name="FAQ", description="Gestion des questions fréquentes")
     * @Doc\Response(response=200, description="[A CHANGER] OK")
     */
    public function updateQuestion() {}

}