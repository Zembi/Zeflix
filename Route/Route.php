<?php

class Route {
    private array $pages = [];
    private string $currentFile;

    public function __construct(?string $currentFile) {
        $this->currentFile = $currentFile;
    }

    public function getCurrentFileName(): ?string {
        if($this->currentFile) return basename($this->currentFile);
        return null;
    }

    public function addPages(array $pages): void {
        foreach($pages as $index => $page) {
            $this->pages[$index] = $page;
        }
    }

    public function removePage(string $pageToRmv): ?array {
        foreach($this->pages as $index => $page) {
            if($page === $pageToRmv) {
                $item = [$index => $this->pages[$index]];
                unset($this->pages[$index]);
                return $item;
            }
        }
        return null;
    }

    public function redirectToPage(?string $targetPage): void {
        if(!$targetPage || empty($targetPage)) {
            $this->redirectToRoot();
            return;
        }

        foreach($this->pages as $index => $page) {
            if($page === $targetPage) {
                header('Location: /Pages/'.$this->pages[$index]);
            }
        }
    }

    public function redirectToRoot(): void {
        $targetPage = 'index.php';
        foreach($this->pages as $index => $page) {
            if($page === $targetPage) {
                header('Location: '.$this->pages[$index]);
            }
        }
    }
}