<?php


namespace App\Entity\Pojo;


use App\Entity\HTMLSection;

class PageContentOut
{
    /** @var int */
    private $id;
    /** @var string $title */
    private $title;
    /** @var string $html */
    private $html;

    function __construct(HTMLSection $origin)
    {
        $this->id = $origin->getId();
        $this->title = $origin->getTitle();
        $this->html = $origin->getHtml();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getHtml(): ?string
    {
        return $this->html;
    }
}