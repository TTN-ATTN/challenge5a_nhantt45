<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class Message
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function sendMessage($senderId, $receiverId, $content)
    {
        $stmt = $this->db->prepare("INSERT INTO messages (sender_id, receiver_id, content) VALUES (?, ?, ?)");
        return $stmt->execute([$senderId, $receiverId, $content]);
    }

    public function getMessagesByReceiverId($receiverId)
    {
        $stmt = $this->db->prepare("
            SELECT m.id, m.sender_id, m.content, m.created_at, u.full_name as sender_name, u.avatar as sender_avatar 
            FROM messages m 
            JOIN users u ON m.sender_id = u.id 
            WHERE m.receiver_id = ? 
            ORDER BY m.created_at DESC
        ");
        $stmt->execute([$receiverId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateMessage($messageId, $senderId, $content)
    {
        $stmt = $this->db->prepare("UPDATE messages SET content = ? WHERE id = ? AND sender_id = ?");
        return $stmt->execute([$content, $messageId, $senderId]);
    }

    public function deleteMessage($messageId, $senderId)
    {
        $stmt = $this->db->prepare("DELETE FROM messages WHERE id = ? AND sender_id = ?");
        return $stmt->execute([$messageId, $senderId]);
    }
}