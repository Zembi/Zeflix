<?php

class Route {
    /** @var Page[] */
    private array $all_pages = [];
    /** @var Page[] */
    public array $pages_only_for_users = [];
    /** @var Page[] */
    public array $pages_only_for_visitors = [];

    private string $currentFilePath;
    private string $currentFileName;

    private Page $currentPage;
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
    public function getCurrentPage(): Page {
        return $this->currentPage;
    }

    public function getCurrentPageName(): ?string {
        return $this->currentPage->getName();
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
            if($file && $this->isPageAlreadyAdded($page_name)) {
                continue;
            }

            $isCurrPage = $this->findPageFromFilePath($this->currentFilePath, $page->getFilePath());
            if($isCurrPage) {
                $this->currentPage = $page;
                $this->isCurrentPagePublic = $page->isPublic();
            }

            $this->all_pages[$page->getName()] = $page;

            if($page->isOnlyUsers()) {
                $this->pages_only_for_users[$page->getName()] = $page;
            }
            else if($page->isOnlyVisitors()) {
                $this->pages_only_for_visitors[$page->getName()] = $page;
            }
        }
    }

    private function isPageAlreadyAdded(string $page_name): bool {
        foreach($this->all_pages as $existingPage) {
            if($existingPage->getName() === $page_name) return true;
        }
        return false;
    }

    private function findPageFromFilePath(string $absolutePath, string $relativePath): bool {
        $basePath = "/var/www/html/";
        $normalizedAbsolute = strtolower(str_replace($basePath, "", $absolutePath));
        $normalizedRelative = strtolower($relativePath);

        return $normalizedAbsolute === $normalizedRelative;
    }

    public function redirectToPage(?string $targetPage): void {
        if(!$targetPage || empty($targetPage) || $targetPage === 'Root') {
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
            if($page->getName() === 'Root') {
                header('Location: /'.$page->getFile());
            }
        }
    }

    public function getPageLink(string $page_name): string {
        foreach($this->all_pages as $page) {
            if($page->getName() === $page_name) {
                return $page->getPageLink();
            }
        }
        return '/';
    }
}