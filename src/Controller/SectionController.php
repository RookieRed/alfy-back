<?php

namespace App\Controller;

use App\Entity\HTMLSection;
use App\Service\SectionService;
use App\Service\ValidationService;
use App\Utils\JsonSerializer;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as Doc;

/**
 * Class SectionController
 * @package App\Controller
 * @Route(path="/pages-sections")
 * @Doc\Tag(name="Pages", description="Gestion des pages du site.")
 */
class SectionController extends JsonAbstractController
{

    /**
     * SectionController constructor.
     * @param SectionService $sectionService
     */
    private $sectionService;
    /**
     * @var ValidationService
     */
    private $validationService;

    public function __construct(
        JsonSerializer $serializer,
        ValidationService $validationService,
        SectionService $sectionService)
    {
        parent::__construct($serializer);
        $this->sectionService = $sectionService;
        $this->validationService = $validationService;
    }

    /**
     * @Route(path="/{idPage}", methods={"POST"}, name="create_page_section")
     * @Doc\Tag(name="Pages", description="Gestion des pages du site.")
     * @Doc\Parameter(
     *     in="body", required=true, name="sectionBean",
     *     description="La représentation JSON de la section à créer.",
     *     @Model(type=App\Entity\Section::class, groups={"create_page_section"})
     * )
     * @Doc\Response(
     *     response=201,
     *     description="Section créée.",
     *     @Model(type=App\Entity\Section::class, groups={"get_page"})
     * )
     * @Doc\Response(response=400, description="Argument ou bean incorrect.")
     * @Doc\Response(response=404, description="Page parente non trouvée.")
     */
    public function createPageSection(Request $request)
    {
        $this->sectionService;
    }

    /**
     * @Route(path="/{sectionId}", methods={"PUT"},  requirements={"id"="\d+"}, name="update_page_section")
     * @Doc\Tag(name="Pages", description="Gestion des pages du site.")
     * @Doc\Parameter(
     *     in="path", required=true, name="sectionId", type="integer",
     *     description="L'id de la section de page à modifier."
     * )
     * @Doc\Parameter(
     *     in="body", required=true, name="sectionBean",
     *     description="La représentation JSON de la section à modifier.",
     *     @Model(type=App\Entity\Section::class, groups={"update_page_section"})
     * )
     * @Doc\Response(
     *     response=200,
     *     description="Section modifiée.",
     *     @Model(type=App\Entity\Section::class, groups={"get_page"})
     * )
     * @Doc\Response(response=400, description="Argument ou bean incorrect.")
     * @Doc\Response(response=404, description="Section de page non trouvée.")
     */
    public function updatePageSection(Request $request)
    {
        $sectionId = $request->get('sectionId');
        $sectionToUpdate = $this->sectionService->findByIdOr404(+$sectionId);

        $sectionBean = json_decode($request->getContent());
        $this->validationService->validateOrThrowException($sectionBean);

        $updatedSection = $this->sectionService->updateSection($sectionBean, $sectionToUpdate);

        return $this->json($updatedSection, Response::HTTP_OK, ["get_page"]);
    }

    /**
     * @Route(path="/{sectionId}",
     *     methods={"DELETE"},
     *     name="page_section_delete",
     *     requirements={"sectionId"="\d+"}
     * )
     * @Doc\Tag(name="Pages", description="Gestion des pages du site.")
     * @Doc\Parameter(name="sectionId", type="string",
     *     description="ID de la section de page à supprimer.", required=true, in="path")
     * @Doc\Response(response=404, description="La section de page n'existe pas.")
     * @Doc\Response(response=204, description="Suppression OK.")
     */
    public function delete(Request $request)
    {

    }
}
