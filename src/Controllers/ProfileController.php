<?php

namespace App\Controllers;

use App\Models\User;
use App\Core\Session;
use App\Controllers\ErrorController;

class ProfileController
{
    public function showProfile()
    {
        $currentUserId = Session::get('user_id');
        if (!$currentUserId) {
            header('Location: /login');
            exit;
        }
        $targetId = $_GET['id'] ?? $currentUserId;
        $userModel = new User();
        $profileUser = $userModel->getUserById($targetId);
        if (!$profileUser) {
            $errorController = new ErrorController();
            $errorController->notFound();
            exit;
        }
        $isOwnProfile = ($currentUserId == $profileUser['id']);
        $currentUserRole = Session::get('role');

        $csrfToken = Session::generateCsrfToken();

        // Lấy thông báo lỗi/thành công từ Session (nếu có) rồi set lại thành null để tránh hiện lại ở lần load sau
        $toastError = Session::get('toast_error');
        $toastSuccess = Session::get('toast_success');
        Session::set('toast_error', null);
        Session::set('toast_success', null);

        require_once __DIR__ . '/../Views/profile.php';
    }

    public function validateUpdateRequest($currentUser, $userId){
        // CSRF
        $csrtToken = $_POST['csrf_token'] ?? '';
        if (!Session::verifyCsrfToken($csrtToken)) {
            (new ErrorController())->forbidden("Yêu cầu không hợp lệ (CSRF token không đúng).");
            return;
        }
        $currentPassword = $_POST['current_password'] ?? '';
        if (empty($currentPassword)) {
            Session::set('toast_error', 'Vui lòng nhập mật khẩu hiện tại để xác nhận!');
            header("Location: /profile?id=$userId");
            exit;
        }

        if (!password_verify($currentPassword, $currentUser['password'])) {
            Session::set('toast_error', 'Mật khẩu hiện tại không chính xác!');
            header("Location: /profile?id=$userId");
            exit;
        }
    }

    public function validateInfo($email, $phoneNumber, $userId, $fullName = null){
        // Validate Email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::set('toast_error', 'Định dạng Email không hợp lệ!');
            header("Location: /profile?id=$userId");
            exit;
        }

        // Validate Số điện thoại
        if (preg_match('/[^0-9+\-\s]/', $phoneNumber)) {
            Session::set('toast_error', 'Số điện thoại chỉ được chứa số và các dấu + -');
            header("Location: /profile?id=$userId");
            exit;
        }
    
        if($fullName !== null){
            $allowedCharacters = '/^[a-zA-ZÀ-ỹ\s]+$/u';
            if (!preg_match($allowedCharacters, $fullName)) {
                Session::set('toast_error', 'Họ tên chỉ được chứa chữ cái và khoảng trắng!');
                header("Location: /profile?id=$userId");
                exit;
            }
        }
    }

    public function updateProfile()
    {
        $userId = Session::get('user_id');
        if (!$userId || Session::get('role') !== 'student') {
            (new ErrorController())->forbidden("Chỉ sinh viên mới được tự cập nhật thông tin.");
            return;
        }
        $userModel = new User();
        $currentUser = $userModel->getUserById($userId);
        
        $this->validateUpdateRequest($currentUser, $userId);

        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $phoneNumber = htmlspecialchars($_POST['phone'] ?? '');

        $this->validateInfo($email, $phoneNumber, $userId);

        $newPassword = !empty($_POST['new_password']) ? password_hash($_POST['new_password'], PASSWORD_BCRYPT) : null;
        $avatarPath = null;

        if (!empty($_POST['avatar_url'])) {
            $url = $_POST['avatar_url'];
            if (preg_match('/^https?:\/\//i', $url)) {
                $avatarPath = htmlspecialchars($url);
            }
        }

        // File upload
        if (isset($_FILES['avatar_file']) && $_FILES['avatar_file']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['avatar_file']['tmp_name'];
            $fileName = $_FILES['avatar_file']['name'];
            $fileSize = $_FILES['avatar_file']['size'];

            if ($fileSize < 2097152) {
                $bannedCharacters = ['..', '/', '\\'];
                foreach ($bannedCharacters as $char) {
                    if (strpos($fileName, $char) !== false) {
                        $fileName = str_replace($char, '', $fileName);
                    }
                }

                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $fileTmpPath);
                $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];

                if (in_array($mime, $allowedMimeTypes)) {
                    $newFileName = md5(time() . $fileName) . '.' . pathinfo($fileName, PATHINFO_EXTENSION);
                    $uploadDir = __DIR__ . '/../../storage/uploads/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    $destPath = $uploadDir . $newFileName;
                    if (move_uploaded_file($fileTmpPath, $destPath)) {
                        $avatarPath = '/api/files/' . $newFileName;
                    }
                } else {
                    Session::set('toast_error', 'Chỉ hỗ trợ upload file ảnh (JPG, PNG, GIF).');
                    header("Location: /profile?id=$userId");
                    exit;
                }
            } else {
                Session::set('toast_error', 'Dung lượng ảnh vượt quá 2MB.');
                header("Location: /profile?id=$userId");
                exit;
            }
        }

        // Lấy lại avatar cũ nếu không có cập nhật mới
        $finalAvatar = $avatarPath ?? $currentUser['avatar'];

        $userModel->updateStudentProfile($userId, $email, $phoneNumber, $finalAvatar, $newPassword);

        Session::set('toast_success', 'Cập nhật thông tin thành công!');
        header("Location: /profile?id=$userId");
        exit;
    }

}
