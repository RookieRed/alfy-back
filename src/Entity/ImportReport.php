<?php
/**
 * Created by PhpStorm.
 * User: celoundou-ngah
 * Date: 04/09/2018
 * Time: 16:15
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Class ImportReport
 * @package App\Service
 *
 * @ORM\Entity()
 */
class ImportReport
{
    /**
     * @var int
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @ORM\Id()
     */
    private $id;
    /**
     * @var File
     * @ORM\OneToOne(targetEntity="App\Entity\File")
     * @ORM\JoinColumn(name="file_id", referencedColumnName="id", nullable=true)
     */
    private $file;
    /**
     * @var \DateTimeInterface
     * @ORM\Column(type="date")
     * @Groups({"import_report"})
     */
    private $date;
    /**
     * @var
     * @ORM\Column(type="text")
     * @Groups({"import_report"})
     */
    private $comments;
    /**
     * @var
     * @ORM\Column(type="integer", length=3)
     * @Groups({"import_report"})
     */
    private $nbImported;
    /**
     * @var
     * @ORM\Column(type="integer", length=3)
     * @Groups({"import_report"})
     */
    private $nbErrors;

    public function __construct()
    {
        $this->date = new \DateTime();
        $this->nbErrors = 0;
        $this->nbImported = 0;
        $this->comments = "";
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return ImportReport
     */
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param File $file
     * @return ImportReport
     */
    public function setFile(File $file): self
    {
        $this->file = $file;
        return $this;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTimeInterface $date
     * @return ImportReport
     */
    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return string
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param mixed $comments
     * @return ImportReport
     */
    public function setComments($comments): self
    {
        $this->comments = $comments;
        return $this;
    }

    /**
     * @return int
     */
    public function getNbImported()
    {
        return $this->nbImported;
    }

    /**
     * @param mixed $nbImported
     * @return ImportReport
     */
    public function setNbImported($nbImported): self
    {
        $this->nbImported = $nbImported;
        return $this;
    }

    /**
     * @return int
     */
    public function getNbErrors()
    {
        return $this->nbErrors;
    }

    /**
     * @param mixed $nbErrors
     * @return ImportReport
     */
    public function setNbErrors($nbErrors): self
    {
        $this->nbErrors = $nbErrors;
        return $this;
    }

    public function incrementErrors()
    {
        $this->nbErrors++;
    }

    public function incrementImported()
    {
        $this->nbImported++;
    }

    public function addComment(string $comments)
    {
        $this->comments .= "\n" . $comments;
    }
}
