<?php

namespace App\Entity\Pojo;

use App\Entity\Page;

class PageOut {

    /** @var string $title */
    private $title;
    /** @var PageContentOut[] $contents */
    private $contents;
    /** @var PageFileOut[] $files */
    private $files;

    public function __construct(Page $page, array $pageFiles)
    {
        $this->title = $page->getName();
        $this->files = [];
        $this->contents = [];
        foreach ($page->getContents() as $pc) {
            $this->contents[] = new PageContentOut($pc);
        }
        foreach ($pageFiles as $pf) {
            $this->files[] = new PageFileOut($pf);
        }
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @return PageFileOut
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * @return PageContentOut
     */
    public function getContents(): array
    {
        return $this->contents;
    }
}
