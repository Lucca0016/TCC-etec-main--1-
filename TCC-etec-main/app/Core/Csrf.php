<?php
namespace App\Core;

class Csrf
{
    public static function getToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(16));
        }
        return $_SESSION['_csrf_token'];
    }

    public static function generateToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $token = bin2hex(random_bytes(16));
        $_SESSION['_csrf_token'] = $token;
        return $token;
    }

    public static function validateToken(?string $token): bool
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($token) || empty($_SESSION['_csrf_token'])) return false;
        $valid = hash_equals($_SESSION['_csrf_token'], (string)$token);
        // Once used, rotate
        unset($_SESSION['_csrf_token']);
        return $valid;
    }
}
