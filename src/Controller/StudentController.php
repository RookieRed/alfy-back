<?php
/**
 * Created by PhpStorm.
 * User: celoundou-ngah
 * Date: 25/06/2018
 * Time: 14:15
 */

namespace App\Controller;


use App\Service\FileService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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

    public function __construct(FileService $fileService,
                                EntityManagerInterface $em)
    {
        $this->fileService = $fileService;
        $this->em = $em;
    }

    /**
     * @Route(path="",
     *     methods={"GET"},
     *     name="user_search")
     */
    public function search()
    {

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
        if ($excel === null) {
            throw new \Exception('There is no uploaded file');
        }

        $file = $this->fileService->saveFile($excel);
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