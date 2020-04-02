<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QuestionAnsweredRepository")
 */
class QuestionAnswered
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"update_question_answered", "get_page"})
     * @Assert\NotNull(groups={"update_question_answered"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=511)
     * @Assert\NotBlank(groups={"create_question_answered", "update_question_answered"})
     * @Groups({"get_page", "update_question_answered", "create_question_answered"})
     */
    private $question;

    /**
     * @ORM\Column(type="string", length=511)
     * @Assert\NotBlank(groups={"create_question_answered", "update_question_answered"})
     * @Groups({"get_page", "update_question_answered", "create_question_answered"})
     */
    private $answer;

    /**
     * @ORM\Column(type="integer", length=511, options={"default" = 0})
     * @Assert\PositiveOrZero(groups={"update_question_answered"})
     * @Assert\NotNull(groups={"update_question_answered"})
     * @Groups({"get_page", "update_question_answered"})
     */
    private $orderIndex;

    /**
     * @var FAQCategory
     * @ORM\ManyToOne(targetEntity="FAQCategory", inversedBy="questions", fetch="LAZY")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @var int
     * @Groups({"create_question_answered", "update_question_answered"})
     * @Assert\NotNull(groups={"create_question_answered"})
     * @Assert\PositiveOrZero(groups={"create_question_answered"})
     */
    private $categoryId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getAnswer(): ?string
    {
        return $this->answer;
    }

    public function setAnswer(string $answer): self
    {
        $this->answer = $answer;

        return $this;
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

    public function getCategory(): ?FAQCategory
    {
        return $this->category;
    }

    public function setCategory(?FAQCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getCategoryId(): ?int
    {
        return $this->categoryId;
    }

    public function setCategoryId(?int $categoryId): self
    {
        $this->categoryId = $categoryId;
        return $this;
    }

    public function getPage(): Page {
        return $this->category->getPage();
    }
}
