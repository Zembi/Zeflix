<?php

class Route {
    /** @var Page[] */
    private array $all_pages = [];
    /** @var Page[] */
    private array $public_pages = [];
    /** @var Page[] */
    private array $private_pages = [];

    private string $currentFilePath;
    private string $currentFileName;
    private string $currentPageName;
    private ?bool $isCurrentPagePublic;

    public function __construct(string $currentFilePath) {
        $this->currentFilePath = $currentFilePath;
        $this->currentFileName = strtolower(basename($currentFilePath));
    }

    public function getCurrentFilePath(): ?string {
        return $this->currentFilePath;
    }

    public function getCurrentFileName(): ?string {
        return $this->currentFileName;
    }

    public function getCurrentPageName(): ?string {
        return $this->currentPageName;
    }

    public function isCurrentPagePublic(): ?bool {
        return $this->isCurrentPagePublic;
    }

    public function addAllPages(array $pages): void {
        foreach ($pages as $page) {
            if(!$page instanceof Page) {
                throw new InvalidArgumentException("All items in the pages array must be instances of Page.");
            }
//            AVOID DUPLICATE ENTRIES IN ROUTING BASED ON FILE NAME
            $file = $page->getFile();
            $page_name = $page->getName();
            if ($file && $this->isPageAlreadyAdded($page_name)) {
                continue;
            }

            if($this->findPageFromFilePath($this->currentFilePath, $page->getFilePath())) $this->currentPageName = $page_name;

            if($file === $this->getCurrentFileName()) {
                $this->isCurrentPagePublic = $page->isPublic();
            }

            $this->all_pages[$page->getName()] = $page;

            if(!$page->isPublic()) {
                $this->private_pages[$page->getName()] = $page;
            }
            else {
                $this->public_pages[$page->getName()] = $page;
            }
        }
    }

    private function isPageAlreadyAdded(string $page_name): bool {
        foreach($this->all_pages as $existingPage) {
            if($existingPage->getName() === $page_name) return true;
        }
        return false;
    }

    public function redirectToPage(?string $targetPage): void {
        var_dump($targetPage);
        if(!$targetPage || empty($targetPage) || $targetPage === 'root') {
            $this->redirectToRoot();
            return;
        }

        foreach($this->all_pages as $page) {
            $file = $page->getFile();
            if($page->getName() === $targetPage && $this->currentFileName != $file) {
                header('Location: /Pages/'.$file);
            }
        }
    }

    public function redirectToRoot(): void {
        foreach($this->all_pages as $page) {
            if($page->getName() === 'root') {
                header('Location: /'.$page->getFile());
            }
        }
    }

    public function getPageLink(string $page_name): string {
        foreach($this->all_pages as $index => $page) {
            if($index === $page_name) {
                return $page->getPageLink();
            }
        }
        return '/';
    }

    public function findPageFromFilePath(string $absolutePath, string $relativePath): bool {
        $basePath = "/var/www/html/";
        $normalizedAbsolute = strtolower(str_replace($basePath, "", $absolutePath));
        $normalizedRelative = strtolower($relativePath);

        return $normalizedAbsolute === $normalizedRelative;
    }
}