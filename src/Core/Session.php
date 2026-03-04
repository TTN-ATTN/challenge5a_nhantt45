<?php
namespace App\Core;

class Session {
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.use_only_cookies', 1); // Chỉ sử dụng cookie để lưu session ID
            ini_set('session.use_strict_mode', 1); // Chống session fixation
            session_set_cookie_params([
                'lifetime' => 86400, // 1 ngày
                'path' => '/',
                // 'domain' => $_SERVER['HTTP_HOST'],
                'secure' => false, // Cho phép gửi cookie qua HTTP
                'httponly' => true, // Chỉ cho phép truy cập cookie qua HTTP, chống XSS 
                'samesite' => 'Strict' // Chống CSRF, chỉ gửi cookie trong cùng site
            ]);

            session_start();
        }
    }

    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public static function get($key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }

    public static function destroy() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
        setcookie(session_name(), '', time() - 3600, '/');
    }

    public static function generateCsrfToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function verifyCsrfToken($token) {
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }
}