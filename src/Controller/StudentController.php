<?php
/**
 * Created by PhpStorm.
 * User: celoundou-ngah
 * Date: 25/06/2018
 * Time: 14:15
 */

namespace App\Controller;


use App\Service\FileService;
use App\Service\PaginationService;
use App\Service\UserService;
use App\Utils\JsonSerializer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Swagger\Annotations as Doc;

/**
 * Class StudentController
 * @package App\Controller
 *
 * @Route(path="/students")
 */
class StudentController extends JsonAbstractController
{

    /**
     * @var FileService
     */
    private $fileService;
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var UserService
     */
    private $userService;
    /**
     * @var PaginationService
     */
    private $pagination;

    public function __construct(FileService $fileService,
                                UserService $userService,
                                PaginationService $pagination,
                                JsonSerializer $serializer,
                                EntityManagerInterface $em)
    {
        parent::__construct($serializer);
        $this->fileService = $fileService;
        $this->userService = $userService;
        $this->em = $em;
        $this->serializer = $serializer;
        $this->pagination = $pagination;
    }

    /**
     * @Route(path="",
     *     methods={"GET"},
     *     name="students_search")
     * @param Request $request
     * @return Response
     * @Doc\Tag(name="Étudiants", description="Gestion des étuidants / élèves.")
     * @Doc\Response(response=200, description="[A CHANGER] OK")
     */
    public function search(Request $request)
    {
        $matches = $this->userService->findByName($request->get('search'));
        $paginatedResults = $this->pagination->generatePaginatedResults($request, $matches);
        return $this->jsonOK($paginatedResults, ['user_get_list', 'pagination']);
    }

    /**
     * @Route(path="/import",
     *     methods={"POST"},
     *     name="user_import")
     * @Doc\Tag(name="Étudiants", description="Gestion des étuidants / élèves.")
     * @Doc\Response(response=200, description="[A CHANGER] OK")
     */
    public function importFromExcel(Request $request)
    {
        $user = $this->userService->getConnectedUser();
        if (!$user->isRole('ADMIN')) {
            throw new AccessDeniedException('You are not admin');
        }

        $excel = $request->files->get('file');
        if ($excel === null) {
            throw new BadRequestHttpException('There is no uploaded file');
        }

        $file = $this->fileService->saveFile($excel, $this->userService->getConnectedUser());
        $report = $this->fileService->importFromExcel($file);
        $this->em->flush();

        return $this->jsonOK($report, ['import_report']);
    }

    /**
     * @Route(path="/import",
     *     methods={"GET"},
     *     name="user_import_get_model")
     * @Doc\Tag(name="Étudiants", description="Gestion des étuidants / élèves.")
     * @Doc\Response(response=200, description="[A CHANGER] OK")
     */
    public function getImportModel()
    {
        $file = $this->fileService->generateExcelImportExample();
        return $this->file($file->getFullPath());
    }

    /**
     * @Route(path="",
     *     methods={"PUT"},
     *     name="user_add")
     * @Doc\Tag(name="Étudiants", description="Gestion des étuidants / élèves.")
     * @Doc\Response(response=200, description="[A CHANGER] OK")
     */
    public function addUser()
    {

    }

}