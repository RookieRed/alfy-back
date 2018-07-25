<?php
/**
 * Created by PhpStorm.
 * User: celoundou-ngah
 * Date: 25/06/2018
 * Time: 14:32
 */

namespace App\Service;

use App\Constants\FileConstants;
use App\Entity\File;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use http\Env\Request;
use PhpOffice\PhpSpreadsheet\Document\Properties;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Row;
use PhpOffice\PhpSpreadsheet\Worksheet\RowCellIterator;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class FileService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var UserRepository
     */
    private $userRepo;

    public function __construct(EntityManagerInterface $em,
                                UserRepository $userRepo)
    {
        $this->em = $em;
        $this->userRepo = $userRepo;
    }

    public function saveFile(\Symfony\Component\HttpFoundation\File\File &$symfonyFile,
                             ?User $owner = null,
                             string $path = FileConstants::UPLOAD_DIR): File
    {
        $hashedName = md5(uniqid()) . '.' . $symfonyFile->guessExtension();
        $symfonyFile = $symfonyFile->move($path, $hashedName);
        if ($symfonyFile === null) {
            throw new \Exception('Can not save file');
        }

        $file = new File();
        $file->setName($hashedName)
            ->setPath($path)
            ->setCreatedAt(new \DateTime());
        if ($owner !== null) {
            $file->setOwner($owner);
        }

        $this->em->persist($file);
        return $file;
    }

    public function importFromExcel(File $file)
    {
        $xlsFile = IOFactory::createReaderForFile($file->getFullPath())
            ->load($file);
        $sheet = $xlsFile->getActiveSheet();

        foreach($sheet->getRowIterator(2) as $row) {
            list($firstName, $lastName, $birthDay, $email, $phone, $bac)
                = iterator_to_array($this->getColumnValues($row));

            $user = new User();
            $user->setFirstName($firstName)
                ->setLastName($lastName)
                ->setUsername(strtolower($firstName . '.' . $lastName)) // TODO : check available username
                ->setPassword(null)
                // TODO ->setBaccalaureate()
                ->setBirthDay(\DateTime::createFromFormat('d/m/Y', $birthDay))
                ->setEmail($email)
                ->setPhone($phone);
        }
    }

    public function generateExcelImportExample(): File
    {
        $xls = new Spreadsheet();
        $sheet = $xls->getActiveSheet();
        $sheet->fromArray([
            FileConstants::XLS_FIRST_NAME,
            FileConstants::XLS_LAST_NAME,
            FileConstants::XLS_BIRTHDAY,
            FileConstants::XLS_EMAIL,
            FileConstants::XLS_PHONE,
            FileConstants::XLS_BAC
        ]);
        // Sizing
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $users = $this->userRepo->findBy([], ['firstName' => 'ASC', 'lastName' => 'ASC'], 10);
        $i = 2;
        foreach ($users as $user) {
            $userArray = [
                $user->getFirstName(),
                $user->getLastName(),
                $user->getBirthDay()->format('d/m/Y'),
                $user->getEmail(),
                $user->getPhone(),
                ($user->getBaccalaureate() == null) ? '' : $user->getBaccalaureate()->getName(),
            ];
            $sheet->fromArray($userArray, null, 'A'.$i);
            $i++;
        }

        $xls->setProperties((new Properties())
            ->setCreated(time())
            ->setCreator('RookieRed')
            ->setDescription('Fichier d\'import d\'utilisateurs pour la base de données ALFY')
            ->setTitle('Feuille d\'import ALFY')
        );

        if (file_exists(FileConstants::MODELS_DIR . FileConstants::GENERATED_XLS)) {
            unlink(FileConstants::MODELS_DIR . FileConstants::GENERATED_XLS);
        }

        $writer = new Xls($xls);
        $writer->save(FileConstants::MODELS_DIR . FileConstants::GENERATED_XLS);
        //IOFactory::createWriter($xls, 'Xls')->save(FileConstants::MODELS_DIR . FileConstants::GENERATED_XLS);
        $file = new File();
        $file->setPath(FileConstants::MODELS_DIR)
            ->setName(FileConstants::GENERATED_XLS)
            ->setCreatedAt(new \DateTime());

        return $file;
    }

    /**
     * @param RowCellIterator $rowIterator
     * @return \Generator
     */
    private function getColumnValues(Row $row)
    {
        foreach ($row->getCellIterator() as $cell) {
            $value = $cell->getValue();
            if ($value) {
                yield $value;
            } else {
                yield;
            }
        }
    }
}