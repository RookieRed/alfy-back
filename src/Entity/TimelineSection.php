<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TimelineSectionRepository")
 */
final class TimelineSection extends Section
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TimelineEvent", mappedBy="timeline", orphanRemoval=true)
     */
    private $events;

    public function __construct()
    {
        $this->events = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|TimelineEvent[]
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(TimelineEvent $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events[] = $event;
            $event->setTimeline($this);
        }

        return $this;
    }

    public function removeEvent(TimelineEvent $event): self
    {
        if ($this->events->contains($event)) {
            $this->events->removeElement($event);
            // set the owning side to null (unless already changed)
            if ($event->getTimeline() === $this) {
                $event->setTimeline(null);
            }
        }

        return $this;
    }
}
