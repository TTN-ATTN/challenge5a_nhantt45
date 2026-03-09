<?php
namespace App\Controllers;

use App\Models\Challenge;
use App\Models\User;
use App\Core\Session;

class ChallengeController
{
    private function checkAuth() {
        $userId = Session::get('user_id');
        $localToken = Session::get('session_token');
        $currentUser = (new User())->getUserById($userId);
        if (!$currentUser || $currentUser['session_token'] !== $localToken) {
            Session::destroy(); header('Location: /login'); exit;
        }
        return $currentUser;
    }

    private function normalizeString($str) {
        // Chuyển về chữ thường
        $str = mb_strtolower(trim($str), 'UTF-8');
        
        // Chuyển đổi các ký tự có dấu thành không dấu
        $unicode = array(
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
        );
        foreach ($unicode as $nonUnicode => $uni) {
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }
        
        $str = preg_replace('/\s+/', ' ', $str);
        
        return $str;
    }

    public function index() {
        $currentUser = $this->checkAuth();
        $challenges = (new Challenge())->getAllChallenges();

        $csrfToken = Session::generateCsrfToken();
        $toastError = Session::get('toast_error');
        $toastSuccess = Session::get('toast_success');
        
        $solvedContent = Session::get('solved_content');
        $solvedId = Session::get('solved_id');
        
        Session::set('toast_error', null);
        Session::set('toast_success', null);
        Session::set('solved_content', null);
        Session::set('solved_id', null);

        require_once __DIR__ . '/../Views/challenges.php';
    }

    public function create() {
        $currentUser = $this->checkAuth();
        if ($currentUser['role'] !== 'teacher' || !Session::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            (new ErrorController())->forbidden(); exit;
        }

        $hint = htmlspecialchars($_POST['hint'] ?? '');
        
        if (!isset($_FILES['challenge_file']) || $_FILES['challenge_file']['error'] !== UPLOAD_ERR_OK) {
            Session::set('toast_error', 'Vui lòng đính kèm file .txt.');
            header("Location: /challenges"); exit;
        }

        $fileName = $_FILES['challenge_file']['name'];
        $fileTmpPath = $_FILES['challenge_file']['tmp_name'];

        // Kiểm tra đuôi file txt
        if (strtolower(pathinfo($fileName, PATHINFO_EXTENSION)) !== 'txt') {
            Session::set('toast_error', 'Chỉ được phép upload file .txt');
            header("Location: /challenges"); exit;
        }

        $answer = $this->normalizeString(pathinfo($fileName, PATHINFO_FILENAME));
        $fileHash = md5($answer);

        $uploadDir = __DIR__ . '/../../storage/challenges/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        if (move_uploaded_file($fileTmpPath, $uploadDir . $fileHash . '.txt')) {
            (new Challenge())->createChallenge($currentUser['id'], $hint, $fileHash);
            Session::set('toast_success', 'Tạo Challenge thành công!');
        } else {
            Session::set('toast_error', 'Lỗi lưu file hệ thống.');
        }

        header("Location: /challenges"); exit;
    }

    public function solve() {
        $currentUser = $this->checkAuth();
        if ($currentUser['role'] !== 'student' || !Session::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            (new ErrorController())->forbidden(); exit;
        }

        $challengeId = $_POST['challenge_id'] ?? 0;
        $studentAnswer = $this->normalizeString($_POST['answer'] ?? '');

        $challenge = (new Challenge())->getChallengeById($challengeId);
        
        if ($challenge) {
            $inputHash = md5($studentAnswer);
            
            // So sánh chuỗi Hash
            if ($inputHash === $challenge['file_hash']) {
                $filePath = __DIR__ . '/../../storage/challenges/' . $inputHash . '.txt';
                
                if (file_exists($filePath)) {
                    // Trả về nội dung bài thơ
                    $content = file_get_contents($filePath);
                    Session::set('solved_content', $content);
                    Session::set('solved_id', $challengeId);
                    Session::set('toast_success', 'Chúc mừng! Đáp án chính xác.');
                } else {
                    Session::set('toast_error', 'Lỗi hệ thống: Không tìm thấy file gốc.');
                }
            } else {
                Session::set('toast_error', 'Đáp án chưa chính xác, thử lại nhé!');
            }
        }

        header("Location: /challenges"); exit;
    }

    public function delete() {
        $currentUser = $this->checkAuth();
        if ($currentUser['role'] !== 'teacher' || !Session::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            (new ErrorController())->forbidden(); exit;
        }

        $challengeId = $_POST['challenge_id'] ?? 0;
        $challenge = (new Challenge())->getChallengeById($challengeId);

        if ($challenge && $challenge['teacher_id'] == $currentUser['id']) {
            $filePath = __DIR__ . '/../../storage/challenges/' . $challenge['file_hash'] . '.txt';
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            
            (new Challenge())->deleteChallenge($challengeId, $currentUser['id']);
            Session::set('toast_success', 'Đã xóa thử thách!');
        }

        header("Location: /challenges"); exit;
    }

    public function edit() {
        $currentUser = $this->checkAuth();
        if ($currentUser['role'] !== 'teacher' || !Session::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            (new ErrorController())->forbidden(); exit;
        }

        $challengeId = $_POST['challenge_id'] ?? 0;
        $hint = htmlspecialchars($_POST['hint'] ?? '');
        
        $challenge = (new Challenge())->getChallengeById($challengeId);

        if ($challenge && $challenge['teacher_id'] == $currentUser['id']) {
            $fileHash = $challenge['file_hash']; 

            // Nếu giáo viên upload file đáp án mới
            if (isset($_FILES['challenge_file']) && $_FILES['challenge_file']['error'] === UPLOAD_ERR_OK) {
                $fileName = $_FILES['challenge_file']['name'];
                
                if (strtolower(pathinfo($fileName, PATHINFO_EXTENSION)) === 'txt') {
                    $rawAnswer = pathinfo($fileName, PATHINFO_FILENAME);
                    $answer = $this->normalizeString($rawAnswer);
                    $newFileHash = md5($answer);

                    $uploadDir = __DIR__ . '/../../storage/challenges/';
                    
                    if (move_uploaded_file($_FILES['challenge_file']['tmp_name'], $uploadDir . $newFileHash . '.txt')) {
                        if ($newFileHash !== $fileHash) {
                            $oldPath = $uploadDir . $fileHash . '.txt';
                            if (file_exists($oldPath)) unlink($oldPath);
                        }
                        $fileHash = $newFileHash;
                    }
                } else {
                    Session::set('toast_error', 'Lỗi: Chỉ hỗ trợ file .txt');
                    header("Location: /challenges"); exit;
                }
            }
            (new Challenge())->updateChallenge($challengeId, $currentUser['id'], $hint, $fileHash);
            Session::set('toast_success', 'Đã cập nhật thử thách!');
        }

        header("Location: /challenges"); exit;
    }
}