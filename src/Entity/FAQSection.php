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
     * @ORM\OneToMany(targetEntity="FAQCategory", mappedBy="faqSection", orphanRemoval=true,
     *     cascade={"persist", "remove"}, fetch="EAGER")
     * @Groups({"get_page", "update_page_section"})
     * @Assert\NotNull()
     * @ORM\OrderBy(value={"orderIndex" = "ASC"})
     */
    private $categories;

    public function __construct()
    {
        parent::__construct();
        $this->categories = new ArrayCollection();
    }

    /**
     * @return FAQCategory[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(FAQCategory $category): self
    {
        if (!$this->categories->contains($category)) {
            $category->setOrderIndex(count($this->categories));
            $this->categories[] = $category;
            $category->setFaqSection($this);
        }

        return $this;
    }

    public function removeCategory(FAQCategory $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
            if ($category->getFaqSection() === $this) {
                $category->setFaqSection(null);
            }
        }

        return $this;
    }

    /**
     * @Groups({"get_page"})
     * @return string
     */
    public function getType()
    {
        return PageConstants::SECTION_TYPE_FAQ;
    }
}
