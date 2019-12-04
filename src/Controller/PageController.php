<?php

namespace App\Controller;

use App\Constants\FileConstants;
use App\Constants\UserRoles;
use App\Entity\HTMLSection;
use App\Entity\Pojo\PageFilesIn;
use App\Repository\FileRepository;
use App\Repository\PageRepository;
use App\Service\FileService;
use App\Service\PageService;
use App\Service\UserService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/pages")
 */
class PageController extends AbstractController
{

    /** @var EntityManagerInterface */
    private $em;

    /** @var FileRepository */
    private $fileRepository;

    /** @var PageRepository */
    private $pageRepository;

    /** @var SerializerInterface */
    private $serializer;

    /** @var FileService */
    private $fileService;

    /** @var PageService */
    private $pageService;

    /** @var UserService */
    private $userService;

    /** @var int Maximal time of living for pages caching */
    const MAX_TTL_PAGE_CACHE = 3600;

    public function __construct(
        EntityManagerInterface $em,
        FileRepository $fileRepository,
        FileService $fileService,
        UserService $userService,
        PageService $pageService,
        PageRepository $pageRepository,
        SerializerInterface $serializer
    )
    {
        $this->em = $em;
        $this->serializer = $serializer;
        $this->fileService = $fileService;
        $this->pageService = $pageService;
        $this->userService = $userService;
        $this->fileRepository = $fileRepository;
        $this->pageRepository = $pageRepository;
    }

    /**
     * @Route(path="/{pageName}",
     *     methods={"GET"},
     *     name="page_get_texts"
     * )
     */
    public function getPage(Request $request)
    {
        $pageName = $request->get('pageName');
        if ($pageName == null) {
            return $this->json('No page name provided', Response::HTTP_BAD_REQUEST);
        }
        $page = $this->pageRepository->findOneBy([
            'name' => $pageName
        ]);
        if ($page === null) {
            return $this->json('The page does not exist', Response::HTTP_NOT_FOUND);
        }

        $response = new JsonResponse(
            $this->serializer->serialize($page, 'json', ['groups' => ['get_page']]),
            Response::HTTP_OK, [], true);
        $response->setSharedMaxAge(self::MAX_TTL_PAGE_CACHE);
        $response->headers->addCacheControlDirective('public');
        $response->headers->addCacheControlDirective('must-revalidate');
        return $response;
    }

    /**
     * @Route(path="/{pageName}/files-config",
     *     methods={"POST"},
     *     name="page_update_files_config"
     * )
     */
    public function updateFilesConfig(Request $request)
    {
        $pageName = $request->get('pageName');
        if ($pageName == null) {
            return $this->json('No page name provided', Response::HTTP_BAD_REQUEST);
        }

        /** @var PageFilesIn $pageBean */
        $pageBean = $this->serializer->deserialize($request->getContent(), PageFilesIn::class, 'json');
        try {
            $this->pageService->updatePageFiles($pageBean);
        } catch (InvalidArgumentException $e) {
            return $this->json('Page does not exist', Response::HTTP_NOT_FOUND);
        }
        return $this->json('', Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route(path="/{pageName}/file",
     *     methods={"POST"},
     *     name="page_upload_file"
     * )
     */
    public function uploadFile(Request $request)
    {
        $user = $this->userService->getConnectedUser();
        if (!$user->isRole(UserRoles::ADMIN)) {
            return $this->json('', Response::HTTP_FORBIDDEN);
        }

        $pageName = $request->get('pageName');
        if ($pageName == null) {
            return $this->json('No page name provided', Response::HTTP_BAD_REQUEST);
        }
        $page = $this->pageRepository->findOneBy(['name' => $pageName]);
        if ($page === null) {
            return $this->json('Page does not exist.', Response::HTTP_NOT_FOUND);
        }

        /** @var File $file */
        $file = $request->files->get('file');
        if ($file == null) {
            return $this->json('No picture found', Response::HTTP_BAD_REQUEST);
        }

        $savedFile = $this->fileService->saveFile($file, $user,
            FileConstants::PAGES_PICTURES_DIR . $pageName . '/');
        // TODO : photos added to sections ?
        $this->em->flush();

//        $out = new PageFileOut($pageFile);
        return $this->json([], Response::HTTP_CREATED);
    }
}
