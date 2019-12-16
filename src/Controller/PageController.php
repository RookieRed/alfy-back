<?php

namespace App\Controller;

use App\Service\FileService;
use App\Service\PageService;
use App\Service\UserService;
use App\Utils\JsonSerializer;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as Doc;

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
     * @Doc\Tag(name="Pages", description="Gestion des pages du site.")
     * @Doc\Parameter(name="pageName", description="Nom de la page demandée", type="string", in="path")
     * @Doc\Response(
     *     response=200,
     *     description="Retourne le contenu de la page dont le nom est passé en paramètres",
     *     @Model(type=App\Entity\Page::class, groups={"get_page"})
     * )
     * @Doc\Response(
     *     response=404,
     *     description="Page non trouvée",
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
}
