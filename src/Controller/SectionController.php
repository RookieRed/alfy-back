<?php

namespace App\Controller;

use App\Service\SectionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SectionController
 * @package App\Controller
 * @Route(path="/pages")
 */
class SectionController extends AbstractController
{

    /**
     * SectionController constructor.
     * @param SectionService $sectionService
     */
    private $sectionService;

    public function __construct(
        SectionService $sectionService
    )
    {
        $this->sectionService = $sectionService;
    }

    /**
     * @Route(path="/{id}", methods={"GET"}, requirements={"id"="\d+"}, name="get_one_page_section")
     */
    public function getPageSection(Request $request)
    {
        $id = $request->get("id");
        if ($id === null) {
            return $this->json("Id can not be null", Response::HTTP_BAD_REQUEST);
        }

        $section = $this->sectionService->findById($id);
        if ($section == null) {
            return $this->json("Page section not found", Response::HTTP_NOT_FOUND);
        }
        return $this->json($section, Response::HTTP_OK, ['groups' => ['']]);
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
     * @Route(path="/{id}", methods={"PUT"}, name="update_page_section")
     */
    public function updatePageSection(Request $request)
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/SectionController.php',
        ]);
    }
}
