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
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"user_get", "user_update"})
     */
    private $line1;

    /**
     * @ORM\ManyToOne(targetEntity="City", cascade={"persist"})
     * @Groups({"user_get", "user_update"})
     * @var City
     */
    private $city;

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

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(City $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry()
    {
        return $this->city !== null ?
            $this->city->getCountry() !== null ? $this->city->getCountry() : null
            : null;
    }

    /**
     * @Groups({"user_get"})
     */
    public function getCityName() {
        return $this->city !== null ? $this->city->getName() : null;
    }

    /**
     * @Groups({"user_get"})
     */
    public function getCountryFrName() {
        return $this->city !== null ? $this->city->getCountryFrName() : null;
    }

    /**
     * @Groups({"user_get"})
     */
    public function getCountryEnName() {
        return $this->city !== null ? $this->city->getCountryEnName() : null;
    }
}
