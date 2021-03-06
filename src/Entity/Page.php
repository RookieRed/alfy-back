<?php

namespace App\Entity;

use App\Entity\Traits\TimedEntityTrait;
use App\Service\FileService;
use App\Service\UserService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PageRepository")
 * @ORM\Cache(region="pages", usage="READ_ONLY")
 */
class Page
{
    use TimedEntityTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get_page", "get_page_list"})
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups({"get_page_list"})
     * @Assert\NotBlank()
     */
    private $link;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Section", mappedBy="page", indexBy="code",
     *     cascade={"persist"}, orphanRemoval=true, fetch="EAGER")
     * @Groups({"get_page"})
     * @var Section[]
     * @ORM\Cache(region="pages_sections", usage="READ_ONLY")
     * @ORM\OrderBy(value={"orderIndex" = "ASC"})
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
     * @return Collection|Section[]
     */
    public function getSections(): Collection
    {
        return $this->sections;
    }

    public function addSection(Section $content): self
    {
        if (!$this->sections->contains($content)) {
            $this->sections[] = $content;
            $content->setPage($this);
            $content->setOrderIndex(count($this->sections));
        }

        return $this;
    }

    public function removeSection(Section $content): self
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

    public function getType()
    {
        // TODO: Implement getType() method.
    }
}
