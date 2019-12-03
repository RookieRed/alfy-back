<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TileRepository")
 */
class Tile
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"get_page"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\File", fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"get_page"})
     */
    private $photo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"get_page"})
     */
    private $link;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get_page"})
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=4095, nullable=true)
     * @Groups({"get_page"})
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TilesSection", inversedBy="tiles", cascade={"remove"})
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private $parentSection;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPhoto(): ?File
    {
        return $this->photo;
    }

    public function setPhoto(?File $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getParentSection(): ?TilesSection
    {
        return $this->parentSection;
    }

    public function setParentSection(?TilesSection $parentSection): self
    {
        $this->parentSection = $parentSection;

        return $this;
    }
}
