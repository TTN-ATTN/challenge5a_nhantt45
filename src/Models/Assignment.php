<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class Assignment
{
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function createAssignment($teacherId, $title, $description, $filePath, $deadline) {
        $stmt = $this->db->prepare("INSERT INTO assignments (teacher_id, title, description, file_path, deadline) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$teacherId, $title, $description, $filePath, $deadline]);
    }

    public function getAllAssignments() {
        $stmt = $this->db->query("
            SELECT a.*, u.full_name as teacher_name 
            FROM assignments a 
            JOIN users u ON a.teacher_id = u.id 
            ORDER BY a.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAssignmentById($id) {
        $stmt = $this->db->prepare("SELECT * FROM assignments WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteAssignment($id, $teacherId) {
        $stmt = $this->db->prepare("DELETE FROM assignments WHERE id = ? AND teacher_id = ?");
        return $stmt->execute([$id, $teacherId]);
    }

    public function submitAssignment($assignmentId, $studentId, $filePath) {
        $stmt = $this->db->prepare("INSERT INTO submissions (assignment_id, student_id, file_path) VALUES (?, ?, ?)");
        return $stmt->execute([$assignmentId, $studentId, $filePath]);
    }

    public function getSubmissionsByAssignment($assignmentId) {
        $stmt = $this->db->prepare("
            SELECT s.*, u.full_name as student_name, u.username 
            FROM submissions s 
            JOIN users u ON s.student_id = u.id 
            WHERE s.assignment_id = ? 
            ORDER BY s.created_at DESC
        ");
        $stmt->execute([$assignmentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSubmissionById($id) {
        $stmt = $this->db->prepare("SELECT * FROM submissions WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function gradeSubmission($submissionId, $score) {
        $stmt = $this->db->prepare("UPDATE submissions SET score = ? WHERE id = ?");
        return $stmt->execute([$score, $submissionId]);
    }

    public function getStudentSubmission($assignmentId, $studentId) {
        $stmt = $this->db->prepare("SELECT * FROM submissions WHERE assignment_id = ? AND student_id = ? ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([$assignmentId, $studentId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteSubmission($id, $studentId) {
        $stmt = $this->db->prepare("DELETE FROM submissions WHERE id = ? AND student_id = ?");
        return $stmt->execute([$id, $studentId]);
    }
}