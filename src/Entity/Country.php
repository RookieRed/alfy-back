<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CountryRepository", readOnly=true)
 */
class Country
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"user_get", "user_update"})
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=2)
     * @Groups({"user_get"})
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=45)
     * @Groups({"user_get"})
     */
    private $enName;

    /**
     * @ORM\Column(type="string", length=45)
     * @Groups({"user_get"})
     */
    private $frName;

    /**
     * @ORM\Column(type="integer", options={"default"=0})
     */
    private $priority;

    /**
     * @ORM\OneToMany(targetEntity="City", fetch="EAGER", mappedBy="country")
     */
    private $towns;

    public function __construct()
    {
        $this->towns = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEnName(): string
    {
        return $this->enName;
    }

    /**
     * @param mixed $enName
     * @return Country
     */
    public function setEnName(string $enName): self
    {
        $this->enName = $enName;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFrName(): string
    {
        return $this->frName;
    }

    /**
     * @param mixed $frName
     * @return Country
     */
    public function setFrName(string $frName): self
    {
        $this->frName = $frName;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority($priority): self
    {
        $this->priority = $priority;
        return $this;
    }


    public function getTowns()
    {
        return $this->towns;
    }
}
