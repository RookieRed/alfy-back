<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PageRepository")
 */
class Page
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get_page"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $link;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Section", mappedBy="page", orphanRemoval=true, fetch="EAGER")
     * @Groups({"get_page"})
     * @var Section[]
     */
    private $sections;

    public function __construct()
    {
        $this->sections = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;

        return $this;
    }

    /**
     * @return Collection|HTMLSection[]
     */
    public function getSections(): Collection
    {
        return $this->sections;
    }

    public function addContent(HTMLSection $content): self
    {
        if (!$this->sections->contains($content)) {
            $this->sections[] = $content;
            $content->setPage($this);
        }

        return $this;
    }

    public function removeContent(HTMLSection $content): self
    {
        if ($this->sections->contains($content)) {
            $this->sections->removeElement($content);
            // set the owning side to null (unless already changed)
            if ($content->getPage() === $this) {
                $content->setPage(null);
            }
        }

        return $this;
    }
}
