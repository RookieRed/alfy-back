<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CityRepository")
 */
class City
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"location_read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"location_read"})
     */
    private $department;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"location_read"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"location_read"})
     */
    private $zipCode;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Country", inversedBy="cities")
     */
    private $country;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(string $zipCode): self
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCountry(): Country
    {
        return $this->country;
    }

    public function getCountryFrName() {
        $country = $this->getCountry();
        return $country !== null ? $country->getFrName() : null;
    }

    public function getCountryEnName() {
        $country = $this->getCountry();
        return $country !== null ? $country->getEnName() : null;
    }
}
