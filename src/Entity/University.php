<?php

namespace App\Entity;

use App\Entity\Traits\AddressedEntityTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UniversityRepository")
 */
class University
{
    use AddressedEntityTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"user_get", "universities_get"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user_get", "universities_get"})
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Study", mappedBy="university", orphanRemoval=true)
     */
    private $studentYears;

    public function __construct()
    {
        $this->studentYears = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Collection|Study[]
     */
    public function getStudentYears(): Collection
    {
        return $this->studentYears;
    }

    public function addStudentYear(Study $study): self
    {
        if (!$this->studentYears->contains($study)) {
            $this->studentYears[] = $study;
            $study->setUniversity($this);
        }

        return $this;
    }

    public function removeStudentYear(Study $study): self
    {
        if ($this->studentYears->contains($study)) {
            $this->studentYears->removeElement($study);
            // set the owning side to null (unless already changed)
            if ($study->getUniversity() === $this) {
                $study->setUniversity(null);
            }
        }

        return $this;
    }

    /**
     * @param mixed $name
     * @return University
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }
}
