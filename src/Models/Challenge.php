<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class Challenge
{
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function createChallenge($teacherId, $hint, $fileHash) {
        $stmt = $this->db->prepare("INSERT INTO challenges (teacher_id, hint, file_hash) VALUES (?, ?, ?)");
        return $stmt->execute([$teacherId, $hint, $fileHash]);
    }

    public function getAllChallenges() {
        $stmt = $this->db->query("
            SELECT c.*, u.full_name as teacher_name 
            FROM challenges c 
            JOIN users u ON c.teacher_id = u.id 
            ORDER BY c.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getChallengeById($id) {
        $stmt = $this->db->prepare("SELECT * FROM challenges WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteChallenge($id, $teacherId) {
        $stmt = $this->db->prepare("DELETE FROM challenges WHERE id = ? AND teacher_id = ?");
        return $stmt->execute([$id, $teacherId]);
    }

    public function updateChallenge($id, $teacherId, $hint, $fileHash) {
        $stmt = $this->db->prepare("UPDATE challenges SET hint = ?, file_hash = ? WHERE id = ? AND teacher_id = ?");
        return $stmt->execute([$hint, $fileHash, $id, $teacherId]);
    }
}