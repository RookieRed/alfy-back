<?php

namespace App\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EventTileRepository")
 */
class EventTile
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"get_page"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TilesEventsSection", inversedBy="events", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Assert\NotNull()
     */
    private $parentSection;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups({"get_page"})
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get_page"})
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\File", fetch="EAGER", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     * @Groups({"get_page"})
     */
    private $photo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"get_page"})
     */
    private $link;

    /**
     * @ORM\Column(type="string", length=511, nullable=true)
     * @Groups({"get_page"})
     */
    private $description;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParentSection(): ?TilesEventsSection
    {
        return $this->parentSection;
    }

    public function setParentSection(?TilesEventsSection $parentSection): self
    {
        $this->parentSection = $parentSection;

        return $this;
    }

    public function getDate(): ?DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(DateTimeInterface $date): self
    {
        $this->date = $date;

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
}
