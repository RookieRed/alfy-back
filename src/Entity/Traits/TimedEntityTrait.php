<?php

namespace App\Entity\Traits;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

trait TimedEntityTrait
{
    /**
     * @ORM\Column(type="datetime", nullable=false, options={"default" = "CURRENT_TIMESTAMP"})
     */
    private $createdAt;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime|null $dateTime
     * @return TimedEntityTrait
     * @throws \Exception
     */
    public function setCreatedAt(?DateTime $dateTime): self
    {
        $this->createdAt = $createdAt ?? new DateTime();
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTime|null $dateTime
     * @return TimedEntityTrait
     * @throws \Exception
     */
    public function setUpdatedAt(?DateTime $dateTime): self
    {
        $this->updatedAt = $updatedAt ?? new DateTime();
        return $this;
    }

    /**
     * @ORM\PreUpdate()
     */
    public function onPersistEntity() {
        $this->setCreatedAt(null);
        $this->setUpdatedAt(null);
    }

    /**
     * @ORM\PreUpdate()
     */
    public function onUpdateEntity() {
        $this->setUpdatedAt(null);
    }
}