<?php

namespace App\Controller;

use App\Entity\HTMLSection;
use App\Service\SectionService;
use App\Utils\JsonSerializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SectionController
 * @package App\Controller
 * @Route(path="/pages-sections")
 */
class SectionController extends AbstractController
{

    /**
     * SectionController constructor.
     * @param SectionService $sectionService
     */
    private $sectionService;

    public function __construct(
        JsonSerializer $serializer,
        SectionService $sectionService)
    {
        parent::__construct($serializer);
        $this->sectionService = $sectionService;
    }

    /**
     * @Route(path="", methods={"POST"}, name="create_page_section")
     */
    public function createPageSection(Request $request)
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/SectionController.php',
        ]);
    }

    /**
     * @Route(path="/{id}", methods={"PUT"},  requirements={"id"="\d+"}, name="update_page_section")
     */
    public function updatePageSection(Request $request)
    {

        $pageName = $request->get('pageName');
        $contentId = $request->get('contentId');
        if ($pageName == null || $contentId == null) {
            return $this->json('Bad url params', Response::HTTP_BAD_REQUEST);
        }
        $contentPojo = $this->serializer->deserialize($request->getContent(),
            HTMLSection::class, 'json', ['groups' => ['update_page_content']]);

//        $pageContent = $this->pageContentRepository->findOneBy(['id' => $contentId]);
//        if ($pageContent === null) {
//            return $this->json('Page or content not found', Response::HTTP_NOT_FOUND);
//        }
//        TODO
//        $pageContent->setHtml($contentPojo->getHtml());
//        $pageContent->setTitle($contentPojo->getTitle());
//        $pageContent->setUpdatedAt(new DateTime());
//        $pageContent->setLastWriter($this->userService->getConnectedUser());
//        $this->em->flush();

        return $this->json('', Response::HTTP_NO_CONTENT);
    }
}
