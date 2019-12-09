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
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

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
     */
    public function search(Request $request)
    {
        $matches = $this->userService->findByName($request->get('search'));
        $paginatedResults = $this->pagination->generatePaginatedResults($request, $matches);

        $jsonResponse = new Response(
            $this->serializer->serialize($paginatedResults, 'json', ['groups' => ['user_get_list', 'pagination']])
        );
        $jsonResponse->headers->set('Content-type', 'application/json');
        return $jsonResponse;
    }

    /**
     * @Route(path="/import",
     *     methods={"POST"},
     *     name="user_import"
     * )
     */
    public function importFromExcel(Request $request)
    {
        $user = $this->userService->getConnectedUser();
        if (!$user->isRole('ADMIN')) {
            return $this->json('You are not admin', Response::HTTP_FORBIDDEN);
        }

        $excel = $request->files->get('file');
        if ($excel === null) {
            throw new Exception('There is no uploaded file');
        }

        $file = $this->fileService->saveFile($excel, $this->userService->getConnectedUser());
        $report = $this->fileService->importFromExcel($file);
        $this->em->flush();

        $jsonResponse = new Response(
            $this->serializer->serialize($report, 'json', ['groups' => ['import_report']]),
            Response::HTTP_OK
        );
        $jsonResponse->headers->add(['Content-type' => 'application/json']);
        return $jsonResponse;
    }

    /**
     * @Route(path="/import",
     *     methods={"GET"},
     *     name="user_import_get_model"
     * )
     */
    public function getImportModel()
    {
        $file = $this->fileService->generateExcelImportExample();
        return $this->file($file->getFullPath());
    }

    /**
     * @Route(path="",
     *     methods={"PUT"},
     *     name="user_add"
     * )
     */
    public function addUser()
    {

    }

}