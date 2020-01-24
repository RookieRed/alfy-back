<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryFAQRepository")
 */
class CategoryFAQ
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get_page", "update_page_section"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"get_page", "update_page_section"})
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\FAQSection", inversedBy="categories")
     * @Groups({"get_page", "update_page_section"})
     */
    private $faqSection;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $priority;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\QuestionAnswered", mappedBy="category",
     *     orphanRemoval=true)
     * @ORM\OrderBy(value={"priority" = "DESC"})
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

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): self
    {
        $this->priority = $priority;
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
}
