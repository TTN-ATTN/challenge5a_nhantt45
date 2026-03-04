<?php
namespace App\Controllers;

class FileController {
    public function serveAvatar($filename) {
        $filename = basename($filename); // lấy tên file 
        
        $filePath = __DIR__ . '/../../storage/uploads/' . $filename;

        if (!file_exists($filePath)) {
            http_response_code(404);
            die("File not found");
        }

        // Lấy MIME type chuẩn của file để trình duyệt biết cách render
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $filePath);

        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($mime, $allowedMimes)) {
            http_response_code(403);
            die("Forbidden file type");
        }

        // Báo cho trình duyệt biết đây là file ảnh và cho phép cache
        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($filePath));
        header('Cache-Control: public, max-age=86400');
        readfile($filePath);
        exit;
    }
}