<?php
namespace App\Controllers;

use App\Controllers\ErrorController;
use App\Core\Session;

class FileController {
    public function serveAvatar($filename) {
        $filename = basename($filename); // lấy tên file để chống Path Traversal
        
        $filePath = __DIR__ . '/../../storage/uploads/avatars/' . $filename;

        if (!file_exists($filePath)) {
            (new ErrorController())::notFound();
        }

        // Lấy MIME type chuẩn của file để trình duyệt biết cách render
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $filePath);

        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($mime, $allowedMimes)) {
            (new ErrorController())::forbidden("Forbidden file type");
        }

        // Báo cho trình duyệt biết đây là file ảnh và cho phép cache
        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($filePath));
        header('Cache-Control: public, max-age=86400');
        readfile($filePath);
        exit;
    }

    public function serveAssignmentFile($filename) {
        // Kiểm tra đăng nhập
        if (!Session::get('user_id')) {
            http_response_code(403);
            die("Unauthorized Access. Vui lòng đăng nhập.");
        }

        // Chống Path Traversal
        $filename = basename($filename); 
        $filePath = __DIR__ . '/../../storage/uploads/assignments/' . $filename;

        if (!file_exists($filePath)) {
            (new ErrorController())::notFound();
        }

        // Quét kiểm tra MIME type để đảm bảo không phải file thực thi
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $filePath);

        $bannedMimes = ['text/html', 'application/x-httpd-php', 'application/javascript', 'application/x-sh'];
        if (in_array($mime, $bannedMimes)) {
            (new ErrorController())::forbidden("Forbidden file type.");
        }

        // Báo trình duyệt tải file
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream'); 
        header('Content-Disposition: attachment; filename="' . $filename . '"'); // Hiện popup save file
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        
        readfile($filePath);
        exit;
    }
}