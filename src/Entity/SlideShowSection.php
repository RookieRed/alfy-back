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
 */
class SlideShowSection extends Section
{
    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\File", inversedBy="relatedSlides", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinTable(name="slide_show_sections_files_asso",
     *     joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")})
     * @Groups({"get_page"})
     * @Assert\NotNull()
     */
    private $photos;

    public function __construct()
    {
        parent::__construct();
        $this->photos = new ArrayCollection();
    }

    /**
     * @return Collection|File[]
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function addPhoto(File $file): self
    {
        if (!$this->photos->contains($file)) {
            $this->photos[] = $file;
        }

        return $this;
    }

    public function removePhoto(File $file): self
    {
        if ($this->photos->contains($file)) {
            $this->photos->removeElement($file);
        }

        return $this;
    }

    /**
     * @Groups({"get_page"})
     */
    public function getType()
    {
        return PageConstants::SECTION_TYPE_SLIDES;
    }
}
