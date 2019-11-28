<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TilesSectionRepository")
 */
class TilesSection
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Tile", mappedBy="tiles", orphanRemoval=true)
     */
    private $tiles;

    public function __construct()
    {
        $this->tiles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Tile[]
     */
    public function getTiles(): Collection
    {
        return $this->tiles;
    }

    public function addTitle(Tile $title): self
    {
        if (!$this->tiles->contains($title)) {
            $this->tiles[] = $title;
            $title->setParentSection($this);
        }

        return $this;
    }

    public function removeTitle(Tile $title): self
    {
        if ($this->tiles->contains($title)) {
            $this->tiles->removeElement($title);
            // set the owning side to null (unless already changed)
            if ($title->getParentSection() === $this) {
                $title->setParentSection(null);
            }
        }

        return $this;
    }
}
