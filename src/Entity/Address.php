<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AddressRepository")
 */
class Address
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user_get", "user_update"})
     */
    private $line1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"user_get", "user_update"})
     */
    private $region;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user_get", "user_update"})
     */
    private $city;

    /**
     * @var Country
     * @ORM\ManyToOne(targetEntity="App\Entity\Country")
     * @Groups({"user_get"})
     */
    private $country;

    /**
     * @var int
     * @Groups({"user_update"})
     */
    private $countryId;

    public function getId()
    {
        return $this->id;
    }

    public function getLine1(): ?string
    {
        return $this->line1;
    }

    public function setLine1(string $line1): self
    {
        $this->line1 = $line1;

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(?string $region): self
    {
        $this->region = $region;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function setCountry(Country $country): self
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return int
     */
    public function getCountryId(): ?int
    {
        return $this->countryId;
    }

    /**
     * @param int $countryId
     */
    public function setCountryId(int $countryId): self
    {
        $this->countryId = $countryId;
        return $this;
    }
}
