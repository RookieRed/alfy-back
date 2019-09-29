<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="PageFileRepository")
 */
class PageFile
{

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Page")
     * @ORM\Id()
     * @ORM\JoinColumn(nullable=false)
     */
    private $page;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\File")
     * @ORM\Id()
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"get_page"})
     */
    private $file;

    /**
     * @ORM\Column(type="json", nullable=true)
     * @Groups({"get_page"})
     */
    private $options = [];

    public function getPage(): ?Page
    {
        return $this->page;
    }

    public function setPage(?Page $page): self
    {
        $this->page = $page;

        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getOptions(): ?array
    {
        return $this->options;
    }

    public function setOptions(?array $options): self
    {
        $this->options = $options;

        return $this;
    }
}
