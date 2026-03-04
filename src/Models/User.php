<?php

namespace App\Models;

use App\Config\Database;
use App\Controllers\ErrorController;
use PDO;

class User
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getUserByUsername($username)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllUsers()
    {
        $stmt = $this->db->query("SELECT id, username, full_name, role, email, phone_number FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStudentProfile($id, $email, $phoneNumber, $avatarUrl, $newPassword = null)
    {
        if ($newPassword) {
            $stmt = $this->db->prepare("UPDATE users SET email = ?, phone_number = ?, avatar = ?, password = ? WHERE id = ?");
            return $stmt->execute([$email, $phoneNumber, $avatarUrl, $newPassword, $id]);
        } else {
            $stmt = $this->db->prepare("UPDATE users SET email = ?, phone_number = ?, avatar = ? WHERE id = ?");
            return $stmt->execute([$email, $phoneNumber, $avatarUrl, $id]);
        }
    }

    // # Teacher's operation
    public function updateStudentProfileForTeacher($id, $username, $fullName, $email, $phoneNumber, $newPassword = null){
        if($newPassword){
            $stmt = $this->db->prepare("UPDATE users SET username = ?, password = ?, full_name = ?, email = ?, phone_number = ? WHERE id = ? and role = 'student'");
            return $stmt->execute([$username, $newPassword, $fullName, $email, $phoneNumber, $id]);
        }
        else{
            $stmt = $this->db->prepare("UPDATE users SET username = ?, full_name = ?, email = ?, phone_number = ? WHERE id = ? and role = 'student'");
            return $stmt->execute([$username, $fullName, $email, $phoneNumber, $id]);
        }
    }

    public function createStudent($username, $password, $fullName, $email, $phoneNumber){
        $stmt = $this->db->prepare("INSERT INTO users (username, password, full_name, email, phone_number, role) VALUES (?, ?, ?, ?, ?, 'student')");
        return $stmt->execute([$username, $password, $fullName, $email, $phoneNumber]);
    }

    public function deleteStudent($id){
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ? AND role = 'student'");
        return $stmt->execute([$id]);
    }

    public function updateSessionToken($userId, $token)
    {
        $this->db->beginTransaction();
        try {
            // Chống Race Condition
            // Nếu có 2 request login đến cùng lúc, request đến sau phải chờ request 1 commit xong mới được đọc và ghi.
            $stmtLock = $this->db->prepare("SELECT id FROM users WHERE id = ? FOR UPDATE");
            $stmtLock->execute([$userId]);

            // Cập nhật Token mới 
            $stmtUpdate = $this->db->prepare("UPDATE users SET session_token = ? WHERE id = ?");
            $stmtUpdate->execute([$token, $userId]);
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            ErrorController::serverError();
            return false;
        }
    }
}
