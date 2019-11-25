<?php


namespace App\Entity\Pojo;


class PageFilesIn
{
    /** @var string $pageName */
    private $pageName;
    /** @var PageFileIn[] $filesInfo */
    private $filesInfo;

    /**
     * @return string
     */
    public function getPageName(): string
    {
        return $this->pageName;
    }

    /**
     * @param string $pageName
     */
    public function setPageName(string $pageName): void
    {
        $this->pageName = $pageName;
    }

    /**
     * @return PageFileIn[]
     */
    public function getFilesInfo(): array
    {
        return $this->filesInfo;
    }

    /**
     * @param PageFileIn[] $filesInfo
     */
    public function setFilesInfo(array $filesInfo): void
    {
        $this->filesInfo = $filesInfo;
    }
}