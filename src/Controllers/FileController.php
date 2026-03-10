<?php

namespace App\Controllers;

use App\Controllers\ErrorController;
use App\Core\Session;

class FileController
{
    public function serveAvatar($filename)
    {
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

    public function serveAssignmentFile($filename)
    {
        // Kiểm tra đăng nhập
        if (!Session::get('user_id')) {
            ErrorController::forbidden("Bạn phải đăng nhập để tải file.");
            exit;
        }

        // Chống Path Traversal
        $filename = basename($filename);
        $filePath = __DIR__ . '/../../storage/uploads/assignments/' . $filename;

        if (!file_exists($filePath)) {
            (new ErrorController())::notFound();
            exit;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $filePath);

        $allowedMimes = [
            'application/pdf',                                                          // .pdf
            'application/msword',                                                       // .doc
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',  // .docx
            'text/plain',                                                               // .txt
            'application/zip',                                                          // .zip
            'application/x-rar-compressed',                                             // .rar
            'application/vnd.rar'                                                       // .rar
        ];

        if (!in_array($mime, $allowedMimes)) {
            (new ErrorController())::forbidden("Forbidden file type. Loại tệp này không được phép tải xuống.");
            exit;
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
