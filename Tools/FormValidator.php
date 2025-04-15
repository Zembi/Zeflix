<?php

namespace Tools;

class FormValidator {
    public static function stringGreaterThan(string|bool $string, int $min = 3, int $max = 24): bool|string {
        return $string && strlen($string) >= $min && strlen($string) <= $max ? $string : false;
    }

    public static function validEmail(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function confirmEmail(string $email, string $email2): bool|string {
        return $email === $email2 ? $email : false;
    }

    public static function confirmPassword(string $password, string $password2): bool|string {
        return $password === $password2 ? $password : false;
    }
}