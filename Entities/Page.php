<?php

class Page {
    private int $id;
    private string $name;
    private string $title;
    private string $file_path;
    private string $file_name;
    private string $mask_name;
    private ?bool $public;
    private bool $only_users;
    private bool $only_visitors;

    function __construct(string $name, string $title, string $file_path, string $mask_name, ?bool $public, bool $only_users = false, bool $only_visitors = false) {
        $this->name = $name;
        $this->title = $title;
        $this->file_path = $file_path;
        $this->file_name = basename($file_path);
        $this->mask_name = $mask_name;
        $this->public = $public;
        $this->only_users = $only_users;
        $this->only_visitors = $only_visitors;
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

    public function isPublic(): ?bool {
        return $this->public;
    }

    public function isOnlyForUsers(): bool {
        return $this->only_users;
    }

    public function isOnlyForVisitors(): bool {
        return $this->only_visitors;
    }

    public function getPageLink(): string {
        if($this->name != 'Root') return '/Pages/' .$this->file_name;
        return $this->file_name;
    }

    function getDomainUrl(): string {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
        var_dump($protocol . '://' . $_SERVER['HTTP_HOST']);
        return $protocol . "://" . $_SERVER['HTTP_HOST'];
    }
}