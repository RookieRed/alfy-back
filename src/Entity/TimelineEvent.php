<?php

namespace App\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TimelineEventRepository")
 */
class TimelineEvent
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"get_page"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TimelineSection", inversedBy="events")
     * @ORM\JoinColumn(nullable=false)
     */
    private $timeline;

    /**
     * @ORM\Column(type="date")
     * @Groups({"get_page"})
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get_page"})
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=511, nullable=true)
     * @Groups({"get_page"})
     */
    private $description;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTimeline(): ?TimelineSection
    {
        return $this->timeline;
    }

    public function setTimeline(?TimelineSection $timeline): self
    {
        $this->timeline = $timeline;

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
}
