<?php

namespace App\Controllers;

use App\Models\Assignment;
use App\Models\User;
use App\Core\Session;

class AssignmentController
{
    private function checkAuth()
    {
        $userId = Session::get('user_id');
        $localToken = Session::get('session_token');
        $currentUser = (new User())->getUserById($userId);
        if (!$currentUser || $currentUser['session_token'] !== $localToken) {
            Session::destroy();
            header('Location: /login?error=concurrent');
            exit;
        }
        return $currentUser;
    }

    private function handleFileUpload($fileInputName)
    {
        if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $fileTmpPath = $_FILES[$fileInputName]['tmp_name'];
        $fileName = $_FILES[$fileInputName]['name'];
        $fileSize = $_FILES[$fileInputName]['size'];

        // Max 100MB
        if ($fileSize > 100 * 1024 * 1024) {
            Session::set('toast_error', 'Dung lượng file vượt quá 100MB.');
            return false;
        }

        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExtensions = ['pdf', 'doc', 'docx', 'txt', 'zip', 'rar'];

        if (!in_array($extension, $allowedExtensions)) {
            Session::set('toast_error', 'Chỉ hỗ trợ upload file: pdf, doc, docx, txt, zip, rar.');
            return false;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $fileTmpPath);

        $allowedMimes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'text/plain',
            'application/zip',
            'application/x-rar-compressed'
        ];

        if (!in_array($mime, $allowedMimes)) {
            Session::set('toast_error', 'Nội dung file không hợp lệ');
            return false;
        }

        // Đổi tên file để tránh đụng độ và lỗi Path Traversal
        $newFileName = md5(time() . $fileName . rand(0, 9999)) . '.' . $extension;
        $uploadDir = __DIR__ . '/../../storage/uploads/assignments/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (move_uploaded_file($fileTmpPath, $uploadDir . $newFileName)) {
            return '/api/assignments/' . $newFileName;
        }
        return false;
    }

    public function index()
    {
        $currentUser = $this->checkAuth();
        $assignmentModel = new Assignment();

        $assignments = $assignmentModel->getAllAssignments();

        // Nếu là giáo viên, lấy thêm danh sách bài nộp cho từng bài tập
        $submissions = [];
        if ($currentUser['role'] === 'teacher') {
            foreach ($assignments as $hw) {
                $submissions[$hw['id']] = $assignmentModel->getSubmissionsByAssignment($hw['id']);
            }
        }
        $mySubmissions = [];
        if ($currentUser['role'] === 'student') {
            foreach ($assignments as $hw) {
                $mySubmissions[$hw['id']] = $assignmentModel->getStudentSubmission($hw['id'], $currentUser['id']);
            }
        }
        $csrfToken = Session::generateCsrfToken();
        $toastError = Session::get('toast_error');
        $toastSuccess = Session::get('toast_success');
        Session::set('toast_error', null);
        Session::set('toast_success', null);

        require_once __DIR__ . '/../Views/assignments.php';
    }

    public function create()
    {
        $currentUser = $this->checkAuth();
        if ($currentUser['role'] !== 'teacher') {
            (new ErrorController())->forbidden();
            exit;
        }
        if (!Session::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            (new ErrorController())->forbidden("Lỗi CSRF.");
            exit;
        }

        $title = htmlspecialchars($_POST['title'] ?? '');
        $description = htmlspecialchars($_POST['description'] ?? '');
        $deadline = $_POST['deadline'] ?? '';
        $deadline = date('Y-m-d H:i:s', strtotime($deadline));
        if (!$deadline) {
            Session::set('toast_error', 'Deadline không hợp lệ.');
            header("Location: /assignments");
            exit;
        }

        $filePath = $this->handleFileUpload('assignment_file');
        if ($filePath === false) {
            Session::set('toast_error', 'Có lỗi xảy ra khi upload file. Vui lòng thử lại.');
            header("Location: /assignments");
            exit;
        }
        if ($filePath === null) {
            Session::set('toast_error', 'Vui lòng đính kèm file đề bài.');
            header("Location: /assignments");
            exit;
        }

        (new Assignment())->createAssignment($currentUser['id'], $title, $description, $filePath, $deadline);
        Session::set('toast_success', 'Đã giao bài tập mới!');
        header("Location: /assignments");
        exit;
    }

    public function submit()
    {
        $currentUser = $this->checkAuth();
        if ($currentUser['role'] !== 'student') {
            (new ErrorController())->forbidden();
            exit;
        }
        if (!Session::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            (new ErrorController())->forbidden("Lỗi CSRF.");
            exit;
        }

        $assignmentId = $_POST['assignment_id'] ?? 0;

        $filePath = $this->handleFileUpload('submission_file');
        if ($filePath === false) {
            header("Location: /assignments");
            exit;
        }
        if ($filePath === null) {
            Session::set('toast_error', 'Vui lòng chọn file bài làm để nộp.');
            header("Location: /assignments");
            exit;
        }

        $assignmentModel = new Assignment();
        $currentSubmission = $assignmentModel->getStudentSubmission($assignmentId, $currentUser['id']);
        if ($currentSubmission) {
            $filePath = __DIR__ . '/../../storage/uploads/assignments/' . basename($currentSubmission['file_path']);
            if (file_exists($filePath))
                unlink($filePath);
            $assignmentModel->deleteSubmission($currentSubmission['id'], $currentUser['id']);
        }
        $assignmentModel->submitAssignment($assignmentId, $currentUser['id'], $filePath);

        Session::set('toast_success', 'Nộp bài thành công!');
        header("Location: /assignments");
        exit;
    }

    public function grade()
    {
        $currentUser = $this->checkAuth();
        if ($currentUser['role'] !== 'teacher') {
            (new ErrorController())->forbidden();
            exit;
        }
        if (!Session::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            (new ErrorController())->forbidden("Lỗi CSRF.");
            exit;
        }

        $submissionId = $_POST['submission_id'] ?? 0;
        $score = $_POST['score'] ?? null;

        if ($score !== null && is_numeric($score)) {
            if ($score < 0 || $score > 10) {
                Session::set('toast_error', 'Điểm số phải nằm trong khoảng từ 0 đến 10.');
            } else {
                (new Assignment())->gradeSubmission($submissionId, $score);
                Session::set('toast_success', 'Đã lưu điểm thành công!');
            }
        } else {
            Session::set('toast_error', 'Điểm số không hợp lệ.');
        }

        header("Location: /assignments");
        exit;
    }

    public function deleteAssignment()
    {
        $currentUser = $this->checkAuth();
        if ($currentUser['role'] !== 'teacher' || !Session::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            (new ErrorController())->forbidden();
            exit;
        }

        $assignmentId = $_POST['assignment_id'] ?? 0;
        $model = new Assignment();
        $hw = $model->getAssignmentById($assignmentId);

        if ($hw && $hw['teacher_id'] == $currentUser['id']) {
            $filePath = __DIR__ . '/../../storage/uploads/assignments/' . basename($hw['file_path']);
            if (file_exists($filePath)) unlink($filePath);

            $model->deleteAssignment($assignmentId, $currentUser['id']);
            Session::set('toast_success', 'Đã xóa bài tập!');
        }
        header("Location: /assignments");
        exit;
    }

    public function unsubmit()
    {
        $currentUser = $this->checkAuth();
        if ($currentUser['role'] !== 'student' || !Session::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            (new ErrorController())->forbidden();
            exit;
        }

        $submissionId = $_POST['submission_id'] ?? 0;
        $model = new Assignment();
        $sub = $model->getSubmissionById($submissionId);

        if ($sub && $sub['student_id'] == $currentUser['id']) {
            $filePath = __DIR__ . '/../../storage/uploads/assignments/' . basename($sub['file_path']);
            if (file_exists($filePath)) unlink($filePath);

            $model->deleteSubmission($submissionId, $currentUser['id']);
            Session::set('toast_success', 'Đã gỡ bài nộp!');
        }
        header("Location: /assignments");
        exit;
    }
}
