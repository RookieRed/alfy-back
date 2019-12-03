<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SectionRepository")
 */
final class HTMLSection extends Section
{
    /**
     * @ORM\Column(type="string", length=1024)
     * @Groups({"get_page", "update_page_content"})
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Groups({"get_page", "update_page_content"})
     */
    private $html;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\File", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinTable(name="html_sections_files_asso",
     *     joinColumns={@ORM\JoinColumn(name="html_section_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="file_id", referencedColumnName="id")}
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
     * @return mixed
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     * @return HTMLSection
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

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
}
