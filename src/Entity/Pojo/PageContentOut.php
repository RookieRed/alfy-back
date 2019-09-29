<?php


namespace App\Entity\Pojo;


use App\Entity\PageContent;

class PageContentOut
{
    /** @var string $title */
    private $title;
    /** @var string $html */
    private $html;

    function __construct(PageContent $origin)
    {
        $this->title = $origin->getTitle();
        $this->html = $origin->getHtml();
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