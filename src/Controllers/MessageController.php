<?php
namespace App\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Core\Session;

class MessageController
{
    // Hàm phụ trợ check auth gom lại cho gọn
    private function checkAuthAndCsrf()
    {
        $userId = Session::get('user_id');
        $localToken = Session::get('session_token');
        $currentUser = (new User())->getUserById($userId);

        if (!$currentUser || $currentUser['session_token'] !== $localToken) {
            Session::destroy();
            header('Location: /login');
            exit;
        }

        if (!Session::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            (new ErrorController())->forbidden("Lỗi xác thực CSRF.");
            exit;
        }
        return $userId;
    }

    public function store()
    {
        $senderId = $this->checkAuthAndCsrf();
        $receiverId = $_POST['receiver_id'] ?? 0;
        
        if ($senderId == $receiverId) {
            Session::set('toast_error', 'Hành động từ chối: Không thể tự gửi tin nhắn cho chính mình!');
            header("Location: /profile?id=$receiverId");
            exit;
        }

        $content = trim($_POST['content'] ?? ''); 

        if (empty($content)) {
            Session::set('toast_error', 'Nội dung tin nhắn không được để trống.');
            header("Location: /profile?id=$receiverId");
            exit;
        }

        if (!(new User())->getUserById($receiverId)) {
            Session::set('toast_error', 'Người nhận không tồn tại.');
            header('Location: /');
            exit;
        }

        (new Message())->sendMessage($senderId, $receiverId, $content);
        Session::set('toast_success', 'Gửi tin nhắn thành công!');
        header("Location: /profile?id=$receiverId");
        exit;
    }

    public function update()
    {
        $senderId = $this->checkAuthAndCsrf();
        $messageId = $_POST['message_id'] ?? 0;
        $receiverId = $_POST['receiver_id'] ?? 0;
        $content = trim($_POST['content'] ?? '');

        if (!empty($content)) {
            (new Message())->updateMessage($messageId, $senderId, $content);
            Session::set('toast_success', 'Đã cập nhật tin nhắn!');
        }
        header("Location: /profile?id=$receiverId");
        exit;
    }

    public function delete()
    {
        $senderId = $this->checkAuthAndCsrf();
        $messageId = $_POST['message_id'] ?? 0;
        $receiverId = $_POST['receiver_id'] ?? 0;

        (new Message())->deleteMessage($messageId, $senderId);
        Session::set('toast_success', 'Đã xóa tin nhắn!');
        header("Location: /profile?id=$receiverId");
        exit;
    }
}