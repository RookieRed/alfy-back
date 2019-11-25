<?php


namespace App\Entity\Pojo;


use App\Entity\PageFile;

class PageFileOut
{
    private $id;
    private $path;
    private $options;

    public function __construct(PageFile $pageFile)
    {
        $file = $pageFile->getFile();
        if ($file === null) {
            throw new \InvalidArgumentException('File is null.');
        }
        $this->id = $file->getId();
        $this->path = $file->getFullPath();
        $this->options = $pageFile->getOptions();
    }

    /**
     * @return mixed
     */
    public function getId(): int
    {
        return $this->id;
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