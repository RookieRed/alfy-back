<?php
/**
 * Created by PhpStorm.
 * User: celoundou-ngah
 * Date: 25/06/2018
 * Time: 14:32
 */

namespace App\Service;

use App\Constants\FileConstants;
use App\Constants\UserRoles;
use App\Entity\Baccalaureate;
use App\Entity\File;
use App\Entity\ImportReport;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Document\Properties;
use PhpOffice\PhpSpreadsheet\Exception;
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
    /**
     * @var UserService
     */
    private $userService;

    public function __construct(EntityManagerInterface $em,
                                UserService $userService,
                                UserRepository $userRepo)
    {
        $this->em = $em;
        $this->userService = $userService;
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

    public function importFromExcel(File $file): ImportReport
    {
        $xlsFile = IOFactory::createReaderForFile($file->getFullPath())
            ->load($file->getFullPath());
        $sheet = $xlsFile->getActiveSheet();

        $report = new ImportReport();
        $report->setDate(new \DateTime());

        $persistedUsers = [];
        $i = 2;
        foreach($sheet->getRowIterator(2) as $row) {
            list($firstName, $lastName, $birthDay, $email, $phone, $bac, $username)
                = iterator_to_array($this->getColumnValues($row));
            try {
                // Check parameters
                if ($firstName == null || $lastName == null) {
                    throw new \Exception('first name or last name is not specified');
                }
                if ($email != null
                    && ( $this->userService->emailExists($email)
                    || !$this->isAttributeUnique($persistedUsers, 'getEmail', $email) )) {
                    $email = null;
                    $report->addComment("Line $i : email already exists");
                }
                $email = trim($email);
                $email = strlen($email) > 0 ? $email : null;
                if ($username == null
                    || ( $this->userService->usernameExists($username)
                    || !$this->isAttributeUnique($persistedUsers, 'getUsername', $username) )) {
                    $j = 0;
                    $formattedFN = explode(' ', $firstName)[0];
                    $formattedLN = explode(' ', $lastName)[0];
                    $username = strtolower($formattedFN .'.'. $formattedLN);
                    while ($this->userService->usernameExists($username)
                        || !$this->isAttributeUnique($persistedUsers, 'getUsername', $username)) {
                        $j++;
                        $username = strtolower($formattedFN .'.'. $formattedLN .$j);
                    }
                }
                $username = trim($username);
                $username = strlen($username) > 0 ? $username : null;
                $birthDay = $birthDay != null
                    ? \DateTime::createFromFormat('d/m/Y', $birthDay)
                    : null;
                $bacEntity = $this->em->getRepository(Baccalaureate::class)
                    ->findOneBy(['name' => $bac]);
                $phone = trim($phone);
                $phone = strlen($phone) > 0 ? $phone : null;

                $user = new User();
                $user->setFirstName($firstName)
                    ->setLastName($lastName)
                    ->setUsername($username)
                    ->setPassword(null)
                    ->setBaccalaureate($bacEntity)
                    ->setBirthDay($birthDay != null
                        ? \DateTime::createFromFormat('d/m/Y', $birthDay)
                        : null)
                    ->setEmail($email)
                    ->setPhone($phone)
                    ->setRole(UserRoles::STUDENT);

                $this->em->persist($user);
                $persistedUsers[] = $user;
                $report->incrementImported();
            }
            catch (\Exception $e) {
                $report->incrementErrors();
                $report->addComment("Line $i : ". $e->getMessage());
            } finally {
                $i++;
            }
        }

        $this->em->persist($report);
        return $report;
    }

    public function generateExcelImportExample(): File
    {
        $xls = new Spreadsheet();
        $sheet = $xls->getActiveSheet();
        $columnsName = [
            FileConstants::XLS_FIRST_NAME,
            FileConstants::XLS_LAST_NAME,
            FileConstants::XLS_BIRTHDAY,
            FileConstants::XLS_EMAIL,
            FileConstants::XLS_PHONE,
            FileConstants::XLS_BAC,
            FileConstants::XLS_USERNAME,
        ];
        $worksheet = $sheet->fromArray($columnsName);
        // Sizing
        $range = range('A', chr(ord('A') + count($columnsName)));
        foreach ($range as $col) {
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
                $user->getUsername(),
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

    private function isAttributeUnique(array $entities, string $getterName, $value): bool {
        if ($value == null || $entities === []) {
            return true;
        }
        return array_reduce($entities,
            function($carry, $actual) use ($getterName, $value) {
                return $carry && ($actual->$getterName() != $value);
            }, true);
    }
}