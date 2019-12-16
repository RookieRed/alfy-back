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
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=511, unique=true)
     * @Assert\Unique()
     * @Assert\NotBlank()
     * @Groups({"get_page", "update_question_answered", "create_question_answered"})
     */
    private $question;

    /**
     * @ORM\Column(type="string", length=511)
     * @Assert\NotBlank()
     * @Groups({"get_page", "update_question_answered", "create_question_answered"})
     */
    private $answer;

    /**
     * @ORM\Column(type="integer", length=511, options={"default" = 0})
     * @Assert\Positive()
     * @Groups({"get_page", "update_question_answered", "create_question_answered"})
     */
    private $priority;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\FAQSection", inversedBy="faq")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $parentSection;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPriority()
    {
        return $this->priority;
    }

    public function setPriority($priority): self
    {
        $this->priority = $priority;

        return $this;
    }
}
