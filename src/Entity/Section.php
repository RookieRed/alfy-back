<?php

namespace App\Entity;

use App\Entity\Traits\TimedEntityTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *      "html"="App\Entity\HTMLSection",
 *      "slides"="App\Entity\SlideShowSection",
 *      "timeline"="App\Entity\TilesEventsSection",
 * })
 * @ORM\Cache(region="pages_sections", usage="READ_ONLY")
 */
abstract class Section
{
    use TimedEntityTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"get_page"})
     */
    private $id;

    /**
     * @Groups({"get_page"})
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     * @Groups({"get_page"})
     */
    private $lastWriter;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    private $creator;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Page", inversedBy="sections", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $page;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get_page"})
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @ORM\Version()
     * @ORM\Column(type="integer")
     */
    private $version;

    /**
     * @Groups({"get_page"})
     */
    private $type;

    public function __construct()
    {
        $this->setCreatedAt(null);
    }

    public function getId()
    {
        return $this->id;
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

    public function getLastWriter(): ?User
    {
        return $this->lastWriter;
    }

    public function setLastWriter(?User $lastWriter): self
    {
        $this->lastWriter = $lastWriter;

        return $this;
    }

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator(?User $creator): self
    {
        $this->creator = $creator;

        return $this;
    }

    public function getPage(): ?Page
    {
        return $this->page;
    }

    public function setPage(?Page $page): self
    {
        $this->page = $page;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
