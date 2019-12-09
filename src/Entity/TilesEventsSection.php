<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Class TimelineSection
 * @package App\Entity
 * @ORM\Entity(repositoryClass="App\Repository\SectionRepository")
 * @ORM\Cache(region="pages_sections", usage="READ_ONLY")
 */
final class TilesEventsSection extends Section
{
    /**
     * @ORM\OneToMany(targetEntity="EventTile", mappedBy="parentSection", cascade={"persist"},
     *      fetch="EAGER", orphanRemoval=true)
     * @Groups({"get_page"})
     */
    private $tiles;

    public function __construct()
    {
        parent::__construct();
        $this->tiles = new ArrayCollection();
    }

    public function getTiles(): ArrayCollection
    {
        return $this->tiles;
    }

    public function addTile(EventTile $tile): self
    {
        if (!$this->tiles->contains($tile)) {
            $this->tiles[] = $tile;
            $tile->setParentSection($this);
        }
        return $this;
    }

    public function removeTile(EventTile $tile): self
    {
        if (!$this->tiles->contains($tile)) {
            $this->tiles->removeElement($tile);
            $tile->setParentSection(null);
        }
        return $this;
    }
}