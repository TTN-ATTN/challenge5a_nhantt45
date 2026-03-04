<?php

namespace App\Models;

use App\Config\Database;
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

    public function updateStudentProfile($id, $email, $phone, $avatarUrl, $newPassword = null)
    {
        if ($newPassword) {
            $stmt = $this->db->prepare("UPDATE users SET email = ?, phone_number = ?, avatar = ?, password = ? WHERE id = ?");
            return $stmt->execute([$email, $phone, $avatarUrl, $newPassword, $id]);
        } else {
            $stmt = $this->db->prepare("UPDATE users SET email = ?, phone_number = ?, avatar = ? WHERE id = ?");
            return $stmt->execute([$email, $phone, $avatarUrl, $id]);
        }
    }
}
