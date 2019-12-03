<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HTMLSectionRepository")
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
}
