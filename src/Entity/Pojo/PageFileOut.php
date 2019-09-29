<?php


namespace App\Entity\Pojo;


use App\Entity\PageFile;

class PageFileOut
{
    private $path;
    private $options;

    function __construct(PageFile $pageFile)
    {
        $this->path = $pageFile->getFile()->getFullPath();
        $this->options = $pageFile->getOptions();
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return array|null
     */
    public function getOptions(): ?array
    {
        return $this->options;
    }
}