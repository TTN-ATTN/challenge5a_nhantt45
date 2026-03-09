<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Message;
use App\Core\Session;
use App\Controllers\ErrorController;
use Error;

class ProfileController
{
    public function showProfile()
    {
        $currentUserId = Session::get('user_id');
        $userModel = new User();
        $currentUser = $userModel->getUserById($currentUserId);
        if (!$currentUser) {
            Session::destroy();
            header('Location: /login');
            exit;
        }
        if ($currentUser['session_token'] !== Session::get('session_token')) {
            Session::destroy();
            header('Location: /login?error=concurrent');
            exit;
        }
        $targetId = $_GET['id'] ?? null;
        if (!$targetId || !is_numeric($targetId) || empty($targetId)) {
            $targetId = $currentUserId;
        }
        $profileUser = $userModel->getUserById($targetId);
        if (!$profileUser) {
            ErrorController::notFound("Người dùng không tồn tại.");
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

        $messages = [];
        $messageModel = new Message();
        $messages = $messageModel->getMessagesByReceiverId($targetId);

        require_once __DIR__ . '/../Views/profile.php';
    }

    private function validateRequest($currentUser, $userId)
    {
        if (!$currentUser) {
            Session::destroy();
            header('Location: /login');
            exit;
        }
        if ($currentUser['session_token'] !== Session::get('session_token')) {
            Session::destroy();
            header('Location: /login?error=concurrent');
            exit;
        }
        // CSRF
        $csrtToken = $_POST['csrf_token'] ?? '';
        if (!Session::verifyCsrfToken($csrtToken)) {
            ErrorController::forbidden("Yêu cầu không hợp lệ (CSRF token không đúng).");
            exit;
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

    private function validateInfo($email, $phoneNumber, $location = "/", $fullName = null, $username = null, $isTeacher = false)
    {
        $userModel = new User();

        // validate username
        if ($username !== null) {
            $allowedUsername = '/^[a-zA-Z0-9_]+$/';
            if (!preg_match($allowedUsername, $username)) {
                Session::set('toast_error', 'Tên đăng nhập chỉ được chứa chữ cái, số và dấu gạch dưới!');
                header("Location: $location");
                exit;
            }
            if ($isTeacher && $userModel->getUserByUsername($username)) {
                Session::set('toast_error', 'Tên đăng nhập đã tồn tại, vui lòng chọn tên khác!');
                header("Location: $location");
                exit;
            }
        }

        // Validate Email
        if ($email !== null) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                Session::set('toast_error', 'Định dạng Email không hợp lệ!');
                header("Location: $location");
                exit;
            }
            if ($isTeacher && $userModel->getUserByEmail($email)) {
                Session::set('toast_error', 'Email đã tồn tại, vui lòng sử dụng email khác!');
                header("Location: $location");
                exit;
            }
        }

        // Validate Số điện thoại
        if ($phoneNumber !== null) {
            if (preg_match('/[^0-9+\-\s]/', $phoneNumber)) {
                Session::set('toast_error', 'Số điện thoại chỉ được chứa số và các dấu + -');
                header("Location: $location");
                exit;
            }
            if ($isTeacher && $userModel->getUserByPhoneNumber($phoneNumber)) {
                Session::set('toast_error', 'Số điện thoại đã tồn tại, vui lòng sử dụng số khác!');
                header("Location: $location");
                exit;
            }
        }

        // validate họ tên 
        if ($fullName !== null) {
            $allowedCharacters = '/^[a-zA-ZÀ-ỹ\s]+$/u';
            if (!preg_match($allowedCharacters, $fullName)) {
                Session::set('toast_error', 'Họ tên chỉ được chứa chữ cái và khoảng trắng!');
                header("Location: $location");
                exit;
            }
        }
    }

    public function updateProfile()
    {
        $userId = Session::get('user_id');
        if (!$userId || Session::get('role') !== 'student') {
            ErrorController::forbidden("Chỉ sinh viên mới được tự cập nhật thông tin.");
            return;
        }
        $userModel = new User();
        $currentUser = $userModel->getUserById($userId);

        $this->validateRequest($currentUser, $userId);

        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $phoneNumber = htmlspecialchars($_POST['phone'] ?? '');

        $this->validateInfo($email, $phoneNumber, "/profile?id=$userId");

        if($userModel->getUserByEmail($email) != $currentUser){
            Session::set('toast_error', 'Email đã tồn tại, vui lòng sử dụng email khác!');
            header("Location: /profile?id=$userId");
            exit;
        }

        if($userModel->getUserByPhoneNumber($phoneNumber) != $currentUser){
            Session::set('toast_error', 'Số điện thoại đã tồn tại, vui lòng sử dụng số khác!');
            header("Location: /profile?id=$userId");
            exit;
        }

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
                    $uploadDir = __DIR__ . '/../../storage/uploads/avatars/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    $destPath = $uploadDir . $newFileName;
                    if (move_uploaded_file($fileTmpPath, $destPath)) {
                        $avatarPath = '/api/avatars/' . $newFileName;
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

    private function teacherOperation($operation)
    {
        $userId = Session::get('user_id');
        if (!$userId || Session::get('role') !== 'teacher') {
            ErrorController::forbidden("Chỉ giáo viên mới được thực hiện thao tác này.");
            exit;
        }
        $userModel = new User();
        $currentUser = $userModel->getUserById($userId);

        $this->validateRequest($currentUser, $userId);

        switch ($operation) {
            case 'create':
                $username = htmlspecialchars($_POST['username'] ?? '');
                $fullName = htmlspecialchars($_POST['full_name'] ?? '');
                $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
                $phoneNumber = htmlspecialchars($_POST['phone'] ?? '');
                $password = $_POST['password'] ?? '';

                if (empty($username) || empty($fullName) || empty($email) || empty($phoneNumber) || empty($password)) {
                    Session::set('toast_error', 'Vui lòng nhập đầy đủ thông tin để tạo sinh viên mới!');
                    header("Location: /");
                    exit;
                }

                $this->validateInfo($email, $phoneNumber, "/create-student", $fullName, $username, $isTeacher=true);

                $userModel->createStudent($username, password_hash($password, PASSWORD_BCRYPT), $fullName, $email, $phoneNumber);
                Session::set('toast_success', 'Tạo sinh viên mới thành công!');
                header("Location: /");
                exit;

            case 'edit':
                $targetStudentId = $_POST['student_id'] ?? '';
                $username = htmlspecialchars($_POST['username'] ?? '');
                $fullName = htmlspecialchars($_POST['full_name'] ?? '');
                $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
                $phoneNumber = htmlspecialchars($_POST['phone'] ?? '');
                $newPassword = !empty($_POST['new_password']) ? password_hash($_POST['new_password'], PASSWORD_BCRYPT) : null;

                $this->validateInfo($email, $phoneNumber, "/profile?id=$targetStudentId", $fullName, $username, $isTeacher=true);

                $userModel->updateStudentProfileForTeacher($targetStudentId, $username, $fullName, $email, $phoneNumber, $newPassword);
                Session::set('toast_success', 'Cập nhật thông tin sinh viên thành công!');
                header("Location: /profile?id=$targetStudentId");
                exit;

            case 'delete':
                $targetStudentId = $_POST['student_id'] ?? '';
                $userModel->deleteStudent($targetStudentId);

                Session::set('toast_success', 'Xóa sinh viên thành công!');
                header("Location: /");
                exit;

            default:
                ErrorController::forbidden("Hành động không hợp lệ.");
                exit;
        }
    }

    public function showCreateStudentForm()
    {
        if (Session::get('role') !== 'teacher') {
            (new ErrorController())->forbidden("Chỉ Giáo viên mới có quyền truy cập khu vực này.");
            exit;
        }

        $csrfToken = Session::generateCsrfToken();
        $toastError = Session::get('toast_error');
        $toastSuccess = Session::get('toast_success');
        Session::set('toast_error', null);
        Session::set('toast_success', null);

        require_once __DIR__ . '/../Views/create-student.php';
    }

    public function createStudent()
    {
        $this->teacherOperation("create");
    }

    public function editStudent()
    {
        $this->teacherOperation("edit");
    }

    public function deleteStudent()
    {
        $this->teacherOperation("delete");
    }
}
