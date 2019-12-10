<?php

namespace App\Controller;

use App\Constants\FileConstants;
use App\Constants\UserRoles;
use App\Entity\PageContent;
use App\Entity\PageFile;
use App\Entity\Pojo\PageFileOut;
use App\Entity\Pojo\PageFilesIn;
use App\Entity\Pojo\PageOut;
use App\Repository\PageFileRepository;
use App\Repository\FileRepository;
use App\Repository\PageContentRepository;
use App\Repository\PageRepository;
use App\Service\FileService;
use App\Service\PageService;
use App\Service\UserService;
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

    /** @var EntityManagerInterface */
    private $em;

    /** @var FileRepository */
    private $fileRepository;

    /** @var PageRepository */
    private $pageRepository;

    /** @var PageFileRepository */
    private $filePageRepository;

    /** @var PageContentRepository */
    private $pageContentRepository;

    /** @var SerializerInterface */
    private $serializer;

    /** @var PageContentRepository */
    private $contentRepository;

    /** @var FileService */
    private $fileService;

    /** @var PageService */
    private $pageService;

    /** @var UserService */
    private $userService;


    public function __construct(
        EntityManagerInterface $em,
        FileRepository $fileRepository,
        FileService $fileService,
        UserService $userService,
        PageService $pageService,
        PageRepository $pageRepository,
        PageFileRepository $filePageRepository,
        PageContentRepository $contentRepository,
        SerializerInterface $serializer,
        PageContentRepository $pageContentRepository
    ) {
        $this->em = $em;
        $this->contentRepository = $contentRepository;
        $this->serializer = $serializer;
        $this->fileService = $fileService;
        $this->pageService = $pageService;
        $this->userService = $userService;
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
    public function getPage(Request $request)
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
     * @Route(path="/{pageName}/{contentId}",
     *     methods={"POST"},
     *     name="page_update_contents"
     * )
     */
    public function updateTextSection(Request $request)
    {
        $pageName = $request->get('pageName');
        $contentId = $request->get('contentId');
        if ($pageName == null || $contentId == null){
            return $this->json('Bad url params', Response::HTTP_BAD_REQUEST);
        }
        $contentPojo = $this->serializer->deserialize($request->getContent(),
            PageContent::class, 'json', ['groups' => ['update_page_content']]);

        $pageContent = $this->pageContentRepository->findOneBy(['id' => $contentId]);
        if ($pageContent === null) {
            return $this->json('Page or content not found', Response::HTTP_NOT_FOUND);
        }

        $pageContent->setHtml($contentPojo->getHtml());
        $pageContent->setTitle($contentPojo->getTitle());
        $pageContent->setUpdatedAt(new \DateTime());
        $pageContent->setLastWriter($this->userService->getConnectedUser());
        $this->em->flush();

        return $this->json('', Response::HTTP_NO_CONTENT);
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
        if ($pageName == null){
            return $this->json('No page name provided', Response::HTTP_BAD_REQUEST);
        }

        /** @var PageFilesIn $pageBean */
        $pageBean = $this->serializer->deserialize($request->getContent(), PageFilesIn::class, 'json');
        try {
            $this->pageService->updatePageFiles($pageBean);
        } catch (\InvalidArgumentException $e) {
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
        if ($pageName == null){
            return $this->json('No page name provided', Response::HTTP_BAD_REQUEST);
        }
        $page = $this->pageRepository->findOneBy(['name' => $pageName]);
        if ($page === null) {
            return $this->json('Page does not exist.', Response::HTTP_NOT_FOUND);
        }

        /** @var \Symfony\Component\HttpFoundation\File\File $file */
        $file = $request->files->get('file');
        if ($file == null) {
            return $this->json('No picture found', Response::HTTP_BAD_REQUEST);
        }

        $savedFile = $this->fileService->saveFile($file, $user,
            FileConstants::PAGES_PICTURES_DIR . $pageName . '/');
        $pageFile = new PageFile();
        $pageFile->setPage($page);
        $pageFile->setFile($savedFile);
        $this->em->flush();

        $out = new PageFileOut($pageFile);
        return $this->json($out, Response::HTTP_CREATED);
    }

    /**
     * @Route(path="/{pageName}/files/{fileId}",
     *     methods={"POST"},
     *     name="page_delete_file",
     *     requirements={"fileId"="\d+|me"}
 *     )
     */
    public function deleteFile(Request $request)
    {
        $pageName = $request->get('pageName');
        $fileId = $request->get('fileId');
        if ($pageName == null || $fileId == null){
            return $this->json('Bad url params', Response::HTTP_BAD_REQUEST);
        }

        $page = $this->pageRepository->findOneBy(['name' => $pageName]);
        if ($page === null) {
            return $this->json('Page does not exist.', Response::HTTP_NOT_FOUND);
        }
        $pageFile = $this->filePageRepository->findByPageOrFile($pageName, $fileId);
        if ($pageFile === null) {
            return $this->json('File not found.', Response::HTTP_NOT_FOUND);
        }
        $this->em->remove($pageFile);
        $this->em->flush();
        return $this->json('', Response::HTTP_NO_CONTENT);
    }
}
