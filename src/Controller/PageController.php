<?php

namespace App\Controller;

use App\Constants\FileConstants;
use App\Constants\UserRoles;
use App\Entity\Pojo\PageFilesIn;
use App\Service\FileService;
use App\Service\PageService;
use App\Service\UserService;
use App\Utils\JsonSerializer;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/pages")
 */
class PageController extends JsonAbstractController
{

    /** @var int Maximal time of living for pages caching */
    const MAX_TTL_PAGE_CACHE = 3600;
    /** @var EntityManagerInterface */
    private $em;
    /** @var FileService */
    private $fileService;
    /** @var PageService */
    private $pageService;
    /** @var UserService */
    private $userService;

    public function __construct(
        EntityManagerInterface $em,
        FileService $fileService,
        UserService $userService,
        PageService $pageService,
        JsonSerializer $serializer)
    {
        parent::__construct($serializer);
        $this->em = $em;
        $this->fileService = $fileService;
        $this->pageService = $pageService;
        $this->userService = $userService;
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
            throw new BadRequestHttpException('No page name provided');
        }

        $page = $this->pageService->findByNameOrThrowException($pageName);
        $response = $this->jsonOK($page, ['get_page']);
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
        $pageBean = $this->serializer->jsonDeserialize($request->getContent(), PageFilesIn::class);
        try {
            $this->pageService->updatePageFiles($pageBean);
        } catch (InvalidArgumentException $e) {
            return $this->json('Page does not exist', Response::HTTP_NOT_FOUND);
        }
        return $this->noContent();
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
        $this->pageService->findByNameOrThrowException($pageName);

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
