<?php

namespace App\Controller;

use App\Entity\Pojo\PageOut;
use App\Repository\PageFileRepository;
use App\Repository\FileRepository;
use App\Repository\PageContentRepository;
use App\Repository\PageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/pages",)
 */
class PageController extends AbstractController
{

    /** @var EntityManager */
    private $em;

    /** @var FileRepository */
    private $fileRepository;

    /** @var PageRepository */
    private $pageRepository;

    /** @var PageFile */
    private $filePageRepository;

    /** @var PageContentRepository */
    private $pageContentRepository;

    /** @var SerializerInterface */
    private $serializer;

    /** @var PageContentRepository */
    private $contentRepository;


    public function __construct(
        EntityManagerInterface $em,
        FileRepository $fileRepository,
        PageRepository $pageRepository,
        PageFileRepository $filePageRepository,
        PageContentRepository $contentRepository,
        SerializerInterface $serializer,
        PageContentRepository $pageContentRepository
    ) {
        $this->em = $em;
        $this->contentRepository = $contentRepository;
        $this->serializer = $serializer;
        $this->fileRepository = $fileRepository;
        $this->pageRepository = $pageRepository;
        $this->filePageRepository = $filePageRepository;
        $this->pageContentRepository = $pageContentRepository;
    }

    /**
     * @Route(path="/{pageName}",
     *     methods={"GET"},
     *     name="page_get_texts"
     * )
     */
    public function getTextSections(Request $request)
    {
        $pageName = $request->get('pageName');
        if ($pageName == null){
            return $this->json('No page name provided', Response::HTTP_BAD_REQUEST);
        }
        $page = $this->pageRepository->findOneBy([
            'name' => $pageName
        ]);
        if ($page === null) {
            return $this->json('The page does not exist', Response::HTTP_NOT_FOUND);
        }
        $pageFiles = $this->filePageRepository->findBy([
            'page' => $page
        ]);

        $response = new PageOut($page, $pageFiles);
        $jsonResponse = new JsonResponse($this->serializer->serialize($response, 'json'), Response::HTTP_OK, [],  true);
        return $jsonResponse;
    }

    /**
     * @Route(path="/{pageName}/texts",
     *     methods={"POST"},
     *     name="page_update_texts"
     * )
     */
    public function updateTextSection()
    {

    }

    /**
     * @Route(path="/{pageName}/pictures",
     *     methods={"GET"},
     *     name="page_get_pictures"
     * )
     */
    public function getPictures()
    {
        return [];
    }

    /**
     * @Route(path="/{pageName}/pictures",
     *     methods={"POST"},
     *     name="page_update_pictures"
     * )
     */
    public function updatePictures()
    {

    }
}
