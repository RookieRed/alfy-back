<?php

namespace App\Entity;

use App\Entity\Traits\TimedEntityTrait;
use App\Service\UserService;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *      \App\Constants\PageConstants::SECTION_TYPE_HTML="App\Entity\HTMLSection",
 *      \App\Constants\PageConstants::SECTION_TYPE_SLIDES="App\Entity\SlideShowSection",
 *      \App\Constants\PageConstants::SECTION_TYPE_TILES="App\Entity\TilesEventsSection",
 *      \App\Constants\PageConstants::SECTION_TYPE_FAQ="App\Entity\FAQSection",
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
     * @Groups({"get_page", "create_faq_category"})
     */
    private $id;

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
     * @Groups({"get_page", "update_page_section", "create_page_section"})
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=60, nullable=false, unique=true)
     * @Assert\NotBlank(groups={"section_create"})
     * @Assert\Length(min="2", minMessage="Code must be at least 2 chars long",
     *     max="60", maxMessage="Code must be at most 30 chars long",
     *     groups={"section_create"})
     * @Assert\Regex("/^([a-z]-?)+$/", groups={"section_create"})
     */
    private $code;

    /**
     * @ORM\Version()
     * @ORM\Column(type="integer")
     */
    private $version;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"update_page_section", "create_page_section"})
     */
    private $orderIndex;

    /**
     * @Groups({"create_page_section"})
     * @var string $type
     */
    private $type;

    public function __construct()
    {
        $this->setCreatedAt(null);
    }

    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
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

    public function getOrderIndex()
    {
        return $this->orderIndex;
    }

    public function setOrderIndex($orderIndex): self
    {
        $this->orderIndex = $orderIndex;
        return $this;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function setCode($code): self
    {
        $this->code = $code;
        return $this;
    }

    public abstract function getType();

    public function setType($type): self
    {
        $this->type = $type;

        return $this;
    }

    public function generateCode(): self
    {
        $this->code = strtolower($this->getCode() . '-' .
            trim(strtr(str_replace([' ', '_', "'", ")", "("], '-', $this->title), UserService::ACCENTED_CHAR_MAP)));
        return $this;
    }

    /**
     * @ORM\PrePersist()
     */
    public function onPrePersist()  {
        if ($this->code == null) {
            $this->generateCode();
        }
    }
}
