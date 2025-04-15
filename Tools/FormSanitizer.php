<?php

namespace Tools;

class FormSanitizer {
    public static function sanitizeFormString(string $string, bool $safeMode = false): string {
        $string = strip_tags($string);
        if($safeMode) return $string;
        $string = str_replace(" ", "", $string);
        $string = strtolower($string);
        $string = ucfirst($string);
        return $string;
    }

    public static function sanitizeUsername(string $string, bool $safeMode = false): string {
        $string = strip_tags($string);
        if($safeMode) return $string;
        $string = str_replace(" ", "", $string);
        return $string;
    }

    public static function sanitizePassword(string $string): string {
        $string = strip_tags($string);
        return $string;
    }
}