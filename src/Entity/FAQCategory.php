<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FAQCategoryRepository")
 */
class FAQCategory
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @Assert\NotNull(groups={"update_faq_category"})
     * @ORM\Column(type="integer")
     * @Groups({"get_categories_list", "get_page", "update_faq_category"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(groups={"get_page", "update_page_section", "get_categories_list", "create_faq_category", "update_faq_category"})
     * @Groups({"get_page", "update_page_section", "get_categories_list", "create_faq_category", "update_faq_category"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"get_page", "update_page_section", "get_categories_list", "create_faq_category", "update_faq_category"})
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\FAQSection", inversedBy="categories")
     * @Assert\NotNull()
     * @Groups({"update_page_section"})
     */
    private $faqSection;

    /**
     * @var integer
     * @Assert\NotNull(groups={"create_faq_category"})
     * @Assert\Positive(groups={"create_faq_category"})
     * @Groups({"create_faq_category"})
     */
    private $sectionId;

    /**
     * @ORM\Column(type="integer", nullable=false)
     * @Assert\NotNull(groups={"get_page", "update_order_index", "update_faq_category", "get_categories_list"})
     * @Assert\PositiveOrZero(groups={"get_page", "update_order_index", "update_faq_category", "get_categories_list"})
     * @Groups({"get_page", "update_order_index", "update_faq_category", "get_categories_list"})
     */
    private $orderIndex;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\QuestionAnswered", mappedBy="category",
     *     cascade={"persist"}, orphanRemoval=true)
     * @ORM\OrderBy(value={"orderIndex" = "ASC"})
     * @Groups({"get_page", "update_page_section"})
     */
    private $questions;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId($id): self
    {
        $this->id = $id;
        return $this;
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

    /**
     * @return Collection|QuestionAnswered[]
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(QuestionAnswered $question): self
    {
        if (!$this->questions->contains($question)) {
            $question->setOrderIndex(count($this->questions));
            $this->questions[] = $question;
            $question->setCategory($this);
        }

        return $this;
    }

    public function removeQuestion(QuestionAnswered $question): self
    {
        if ($this->questions->contains($question)) {
            $this->questions->removeElement($question);
            // set the owning side to null (unless already changed)
            if ($question->getCategory() === $this) {
                $question->setCategory(null);
            }
        }

        return $this;
    }

    public function getFaqSection(): FAQSection
    {
        return $this->faqSection;
    }

    public function setFaqSection(FAQSection $faqSection): self
    {
        $this->faqSection = $faqSection;
        return $this;
    }

    public function getOrderIndex(): ?int
    {
        return $this->orderIndex;
    }

    public function setOrderIndex(int $orderIndex): self
    {
        $this->orderIndex = $orderIndex;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return int
     */
    public function getSectionId(): int
    {
        return $this->sectionId;
    }

    /**
     * @param int $sectionId
     */
    public function setSectionId(int $sectionId): void
    {
        $this->sectionId = $sectionId;
    }
}
