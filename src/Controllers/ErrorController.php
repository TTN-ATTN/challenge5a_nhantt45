<?php
namespace App\Controllers;

class ErrorController {
    // Lỗi 404: Không tìm thấy tài nguyên
    public static function notFound($message = "Đường dẫn hoặc trang bạn yêu cầu không tồn tại trên hệ thống.") {
        http_response_code(404);
        $errorMessage = $message;
        require_once __DIR__ . '/../Views/errors/404.php';
    }

    // Lỗi 403: Không có quyền truy cập 
    public static function forbidden($message = "Bạn không có quyền thực hiện hành động này.") {
        http_response_code(403);
        $errorMessage = $message;
        require_once __DIR__ . '/../Views/errors/403.php';
    }

    // Lỗi 500: Lỗi logic server
    public static function serverError($message = "Đã xảy ra lỗi trong quá trình xử lý yêu cầu.") {
        http_response_code(500);
        $errorMessage = $message;
        require_once __DIR__ . '/../Views/errors/500.php';
    }
}