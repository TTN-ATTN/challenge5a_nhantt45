<?php
namespace App\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Core\Session;

class MessageController
{
    public function store()
    {
        // Check Login & Zombie Session
        $senderId = Session::get('user_id');
        $localToken = Session::get('session_token');
        $userModel = new User();
        $currentUser = $userModel->getUserById($senderId);

        if (!$currentUser || $currentUser['session_token'] !== $localToken) {
            Session::destroy();
            header('Location: /login');
            exit;
        }

        // Check CSRF
        if (!Session::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            (new ErrorController())->forbidden("Lỗi xác thực CSRF.");
            exit;
        }

        // Lấy dữ liệu và validate
        $receiverId = $_POST['receiver_id'] ?? 0;
        $content = trim($_POST['content'] ?? ''); 

        if (empty($content)) {
            Session::set('toast_error', 'Nội dung tin nhắn không được để trống.');
            header("Location: /profile?id=$receiverId");
            exit;
        }

        // Kiểm tra người nhận có tồn tại không
        $receiver = $userModel->getUserById($receiverId);
        if (!$receiver) {
            Session::set('toast_error', 'Người nhận không tồn tại.');
            header('Location: /');
            exit;
        }

        // Lưu tin nhắn
        (new Message())->sendMessage($senderId, $receiverId, $content);
        
        Session::set('toast_success', 'Gửi tin nhắn thành công!');
        header("Location: /profile?id=$receiverId");
        exit;
    }
}