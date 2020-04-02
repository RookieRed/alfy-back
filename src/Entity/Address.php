<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @var City
     */
    private $city;

    /**
     * @var int|null
     * @Assert\NotNull(groups={"user_update"})
     * @Assert\Positive(groups={"user_update"})
     * @Groups({"user_update"})
     */
    private $cityId;

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

    public function getCountry(): ?Country
    {
        return $this->city !== null ?
            $this->city->getCountry() !== null ? $this->city->getCountry() : null
            : null;
    }

    /**
     * @Groups({"user_get"})
     */
    public function getCityName(): ?string {
        return $this->city !== null ? $this->city->getName() : null;
    }

    /**
     * @Groups({"user_get"})
     */
    public function getCountryFrName(): ?string {
        return $this->city !== null ? $this->city->getCountryFrName() : null;
    }

    /**
     * @Groups({"user_get"})
     */
    public function getCountryEnName(): ?string {
        return $this->city !== null ? $this->city->getCountryEnName() : null;
    }

    public function getCityId(): ?int
    {
        return $this->cityId;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }
}
