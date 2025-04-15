<?php

namespace Items\Page;

class Private_Page extends Page {
    public function __construct(string $name, string $title, string $file_path, string $mask_name) {
        parent::__construct($name, $title, $file_path, $mask_name);
    }

    public function isForEveryone(): bool {
        return false;
    }

    public function isOnlyForVisitors(): bool {
        return false;
    }

    public function isOnlyForLoggedInUsers(): bool {
        return true;
    }
}