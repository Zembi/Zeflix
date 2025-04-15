<?php

namespace Items\Page;

class Page {
    private int $id;
    private string $name;
    private string $title;
    private string $file_path;
    private string $file_name;
    private string $mask_name;

    function __construct(string $name, string $title, string $file_path, string $mask_name) {
        $this->name = $name;
        $this->title = $title;
        $this->file_path = $file_path;
        $this->file_name = basename($file_path);
        $this->mask_name = $mask_name;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function getFilePath(): string {
        return $this->file_path;
    }

    public function getFile(): string {
        return $this->file_name;
    }

    public function getMaskName(): string {
        return $this->mask_name;
    }

//    function getDomainUrl(): string {
//        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
//        var_dump($protocol . '://' . $_SERVER['HTTP_HOST']);
//        return $protocol . "://" . $_SERVER['HTTP_HOST'];
//    }


//   IMPORTANT FOR ACCESS
    public function isForEveryone(): bool {
        return true;
    }

    public function isOnlyForVisitors(): bool {
        return false;
    }

    public function isOnlyForLoggedInUsers(): bool {
        return false;
    }
}