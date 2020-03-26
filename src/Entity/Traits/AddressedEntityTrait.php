<?php

namespace App\Entity\Traits;

use App\Entity\Address;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait AddressedEntityTrait
{
    /**
     * @var Address|null
     * @ORM\OneToOne(targetEntity="App\Entity\Address", fetch="LAZY")
     * @ORM\JoinColumn(name="address_id", referencedColumnName="id", nullable=true)
     */
    protected $address;

    /**
     * @Groups({"user_get"})
     */
    public function getAddress(): ?Address {
        return $this->address;
    }

    /**
     * @Groups({"user_update"})
     */
    public function setAddress(?Address $address) {
        $this->address = $address;
        return $this;
    }
}