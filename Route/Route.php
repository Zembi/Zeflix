<?php

class Route {
    /** @var Page[] */
    private array $all_pages = [];
    /** @var Page[] */
    public array $pages_only_for_users = [];
    /** @var Page[] */
    public array $pages_only_for_visitors = [];

    private string $currentPageMaskName;

    private Page $currentPage;
    private Page $errorPage;
    private ?bool $isCurrentPagePublic;

    public function __construct(string $currentPageMaskName) {
        $this->currentPageMaskName = trim($currentPageMaskName, '/');
    }

    public function getCurrentPageMaskName(): ?string {
        return $this->currentPageMaskName;
    }

    public function getCurrentPage(): Page {
        return $this->currentPage;
    }

    public function getCurrentPageName(): ?string {
        return $this->currentPage->getName();
    }

    public function getErrorPage(): Page {
        return $this->errorPage;
    }

    public function isTargetPagePublic(): ?bool {
        return $this->isCurrentPagePublic;
    }

    public function handleAllPages(array $pages): void {
        foreach($pages as $page) {
            if(!$page instanceof Page) {
                throw new InvalidArgumentException("All items in the pages array must be instances of Page.");
            }
//            AVOID DUPLICATE ENTRIES IN ROUTING BASED ON FILE NAME
            $file = $page->getFile();
            if($file && $this->isPageAlreadyAdded($page)) {
                continue;
            }

            if($page->getName() === '404') {
                $this->errorPage = $page;
            }

//            FIND CURRENT PAGE
            if($this->getCurrentPageMaskName() === $page->getMaskName()) {
                $this->currentPage = $page;
                $this->isCurrentPagePublic = $page->isPublic();
            }

            $this->all_pages[$page->getMaskName()] = $page;

            if($page->isOnlyForUsers()) {
                $this->pages_only_for_users[$page->getMaskName()] = $page;
            }
            else if($page->isOnlyForVisitors()) {
                $this->pages_only_for_visitors[$page->getMaskName()] = $page;
            }
        }

//        IF CURRENT PAGE IS STILL NULL, SET CURRENT PAGE TO 404
        if(!isset($this->currentPage) && !isset($this->isCurrentPagePublic)) {
            $this->currentPage = $this->getErrorPage();
            $this->isCurrentPagePublic = $page->isPublic();
        }
    }

    private function isPageAlreadyAdded(Page $page_to_check): bool {
        foreach($this->all_pages as $existingPage) {
            if($existingPage->getMaskName() === $page_to_check->getMaskName()) return true;
        }
        return false;
    }

    public function redirectToPage(?Page $targetPage): void {
        if(!isset($targetPage) || $targetPage->getMaskName() === 'root') {
            return;
        }


        $urlPath = '/' . trim($targetPage->getMaskName(), '/');
        header("Location: $urlPath");
        exit();
    }

    public function redirectToRoot(): void {
        header('Location: /' . $this->all_pages['root']->getMaskName());
//        header('Location: /');
    }


//    HELPERS
    public function findPageByMaskName(?string $customName): ?Page {
        if(isset($this->all_pages[$customName])) {
            return $this->all_pages[$customName];
        }

        $segments = explode('/', $customName);
        while(!empty($segments)) {
            $parentPath = implode('/', $segments);
            if(isset($this->all_pages[$parentPath])) {
                return $this->all_pages[$parentPath];
            }
            array_pop($segments);
        }

        return null;
    }

    public function getPageLink(string $page_name): string {
        foreach($this->all_pages as $page) {
            if($page->getName() === $page_name) {
                return '/' . $page->getMaskName();
            }
        }
        return '/';
    }
}