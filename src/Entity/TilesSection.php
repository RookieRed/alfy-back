<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SectionRepository")
 * @ORM\Cache(region="pages_sections.slides", usage="READ_ONLY")
 */
final class TilesSection extends Section
{
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Tile", mappedBy="parentSection",
     *     orphanRemoval=true, fetch="EAGER", cascade={"persist", "remove"})
     * @Groups({"get_page"})
     */
    private $tiles;

    public function __construct()
    {
        $this->tiles = new ArrayCollection();
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
