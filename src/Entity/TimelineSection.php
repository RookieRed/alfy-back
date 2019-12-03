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
final class TimelineSection extends Section
{
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TimelineEvent", mappedBy="timeline", fetch="EAGER", orphanRemoval=true)
     * @Groups({"get_page"})
     */
    private $events;

    public function __construct()
    {
        $this->events = new ArrayCollection();
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
