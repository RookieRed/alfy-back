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
 * @ORM\Table(name="html_section")
 * @ORM\Cache(region="pages_sections", usage="READ_ONLY")
 */
class HTMLSection extends Section
{
    /**
     * @ORM\Column(type="text")
     * @Groups({"get_page", "update_page_content"})
     * @Assert\NotBlank()
     */
    private $html;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\File", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinTable(name="html_sections_files_asso",
     *     joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     * @Groups({"get_page", "update_page_content"})
     * @var File[]
     */
    private $files;

    /**
     * HTMLSection constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->files = new ArrayCollection();
    }


    public function getHtml(): ?string
    {
        return $this->html;
    }

    public function setHtml(string $html): self
    {
        $this->html = $html;

        return $this;
    }

    /**
     * @return Collection|File[]
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(File $file): self
    {
        if (!$this->files->contains($file)) {
            $this->files[] = $file;
        }

        return $this;
    }

    public function removeFile(File $file): self
    {
        if ($this->files->contains($file)) {
            $this->files->removeElement($file);
        }

        return $this;
    }

    /**
     * @Groups({"get_page"})
     */
    public function getType()
    {
        return PageConstants::SECTION_TYPE_HTML;
    }
}
