<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SectionRepository")
 * @ORM\Cache(region="pages_sections", usage="READ_ONLY")
 */
final class SlideShowSection extends Section
{
    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\File", inversedBy="relatedDiapos", cascade={"persist"}, fetch="EAGER")
     * @Groups({"get_page"})
     */
    private $photos;

    public function __construct()
    {
        $this->photos = new ArrayCollection();
    }

    /**
     * @return Collection|File[]
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function addFile(File $file): self
    {
        if (!$this->photos->contains($file)) {
            $this->photos[] = $file;
        }

        return $this;
    }

    public function removeFile(File $file): self
    {
        if ($this->photos->contains($file)) {
            $this->photos->removeElement($file);
        }

        return $this;
    }
}
