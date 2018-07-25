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
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class StudentController
 * @package App\Controller
 *
 * @Route(path="/students")
 */
class StudentController extends Controller
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
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var PaginationService
     */
    private $pagination;

    public function __construct(FileService $fileService,
                                UserService $userService,
                                PaginationService $pagination,
                                SerializerInterface $serializer,
                                EntityManagerInterface $em)
    {
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
        $matches = $this->userService->searchByName($request->get('search'));
        $paginatedResults = $this->pagination->generatePaginatedResults($request, $matches);

        $jsonResponse = new Response(
            $this->serializer->serialize($paginatedResults, 'json', ['groups' => ['user_get_list', 'pagination']])
        );
        $jsonResponse->headers->set('Content-type', 'application/json');
        return $jsonResponse;
    }

    /**
     * @Route(path="/import",
     *     methods={"PUT"},
     *     name="user_import"
     * )
     */
    public function importFromExcel(Request $request)
    {
        $excel = $request->get('file');
        var_dump($request->files); die;
        if ($excel === null) {
            throw new \Exception('There is no uploaded file');
        }

        $file = $this->fileService->saveFile($excel, $this->userService->getConnectedUser());
        $this->fileService->importFromExcel($file);
        $this->em->flush();
    }

    /**
     * @Route(path="/import",
     *     methods={"GET"},
     *     name="user_import_get_model"
     * )
     */
    public function getImportModel() {
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