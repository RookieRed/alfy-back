<?php

namespace App\Entity;

use App\Constants\PageConstants;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SectionRepository")
 * @ORM\Cache(region="pages_sections", usage="READ_ONLY")
 * @ORM\Table(name="faq_section")
 */
class FAQSection extends Section
{
    /**
     * @ORM\OneToMany(targetEntity="QuestionAnswered", mappedBy="parentSection",
     *     cascade={"persist", "remove"}, fetch="EAGER")
     * @Groups({"get_page", "update_page_section"})
     * @Assert\NotNull()
     * @ORM\OrderBy(value={"priority" = "DESC"})
     */
    private $faq;

    public function __construct()
    {
        parent::__construct();
        $this->faq = new ArrayCollection();
    }

    /**
     * @return Collection|File[]
     */
    public function getFaq(): Collection
    {
        return $this->faq;
    }

    public function addQuestion(QuestionAnswered $question): self
    {
        if (!$this->faq->contains($question)) {
            $this->faq[] = $question;
        }

        return $this;
    }

    public function removeQuestion(QuestionAnswered $question): self
    {
        if ($this->faq->contains($question)) {
            $this->faq->removeElement($question);
        }

        return $this;
    }

    /**
     * @Groups({"get_page"})
     */
    public function getType()
    {
        return PageConstants::SECTION_TYPE_FAQ;
    }
}
