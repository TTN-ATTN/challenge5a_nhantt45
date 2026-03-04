<?php
namespace App\Controllers;

class ErrorController {
    // Lỗi 404: Không tìm thấy tài nguyên
    public static function notFound() {
        http_response_code(404);
        require_once __DIR__ . '/../Views/errors/404.php';
    }

    // Lỗi 403: Không có quyền truy cập 
    public static function forbidden($message = "Bạn không có quyền thực hiện hành động này.") {
        http_response_code(403);
        $errorMessage = $message;
        require_once __DIR__ . '/../Views/errors/403.php';
    }

    // Lỗi 500: Lỗi logic server
    public static function serverError() {
        http_response_code(500);
        require_once __DIR__ . '/../Views/errors/500.php';
    }
}